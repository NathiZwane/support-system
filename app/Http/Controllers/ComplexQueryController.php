<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalDetail;
use App\Models\Interest;
use App\Models\PersonalInterest;
use App\Models\Document;

class ComplexQueryController extends Controller
{
    public function index()
    {
        return view('complex-query.index');
    }

    public function generateData()
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Document::truncate();
        PersonalInterest::truncate();
        PersonalDetail::truncate();
        Interest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create interests
        $interestsData = [
            ['name' => 'Gardening', 'category' => 'hobby', 'allows_documents' => true],
            ['name' => 'Animals', 'category' => 'hobby', 'allows_documents' => true],
            ['name' => 'Children', 'category' => 'family', 'allows_documents' => true],
            ['name' => 'Sport', 'category' => 'fitness', 'allows_documents' => false],
            ['name' => 'Fishing', 'category' => 'hobby', 'allows_documents' => false],
            ['name' => 'Reading', 'category' => 'educational', 'allows_documents' => false],
            ['name' => 'Cooking', 'category' => 'lifestyle', 'allows_documents' => false],
            ['name' => 'Travel', 'category' => 'lifestyle', 'allows_documents' => false],
            ['name' => 'Photography', 'category' => 'art', 'allows_documents' => false],
            ['name' => 'Music', 'category' => 'art', 'allows_documents' => false],
            ['name' => 'Painting', 'category' => 'art', 'allows_documents' => false],
            ['name' => 'Dancing', 'category' => 'fitness', 'allows_documents' => false],
            ['name' => 'Yoga', 'category' => 'fitness', 'allows_documents' => false],
            ['name' => 'Meditation', 'category' => 'wellness', 'allows_documents' => false],
            ['name' => 'Writing', 'category' => 'educational', 'allows_documents' => false],
        ];

        foreach ($interestsData as $interestData) {
            Interest::create($interestData);
        }

        $interests = Interest::all();
        $firstNames = ['John', 'Nathi', 'Michael', 'Nosipho', 'David', 'Simo', 'Robert', 'Ayanda', 'William', 'Maria'];
        $lastNames = ['Smith', 'Zwane', 'Williams', 'Yende', 'Jones', 'Hadebe', 'Davis', 'Mokeona', 'Caine', 'Wilson'];

        // Generate 50 people
        for ($i = 1; $i <= 50; $i++) {
            $person = PersonalDetail::create([
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'email' => 'person' . $i . '@example.com',
                'date_of_birth' => date('Y-m-d', rand(-2200000000, -500000000)),
            ]);

            // Assign 3-12 random interests
            $numInterests = rand(3, 12);
            $personInterests = $interests->random($numInterests);

            foreach ($personInterests as $interest) {
                $personalInterest = PersonalInterest::create([
                    'personal_detail_id' => $person->id,
                    'interest_id' => $interest->id,
                ]);

                // Create documents for interests that allow them (60% chance)
                if ($interest->allows_documents && rand(1, 100) <= 60) {
                    $numDocuments = rand(1, 5); // 1-5 documents per interest
                    for ($d = 1; $d <= $numDocuments; $d++) {
                        Document::create([
                            'personal_interest_id' => $personalInterest->id,
                            'file_name' => 'document_' . $person->id . '_' . $interest->id . '_' . $d . '.pdf',
                            'file_path' => '/documents/' . 'document_' . $person->id . '_' . $interest->id . '_' . $d . '.pdf',
                            'file_size' => rand(1000, 1000000),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('complex-query.index')
            ->with('success', 'Data generated successfully! 50 people created with random interests and documents.');
    }

    public function query1()
    {
        // Animal Lovers with only 1 document linked
        $results = PersonalDetail::select('personal_details.*')
            ->join('personal_interests', 'personal_details.id', '=', 'personal_interests.personal_detail_id')
            ->join('interests', 'personal_interests.interest_id', '=', 'interests.id')
            ->join('documents', 'personal_interests.id', '=', 'documents.personal_interest_id')
            ->where('interests.name', 'Animals')
            ->groupBy('personal_details.id')
            ->havingRaw('COUNT(documents.id) = 1')
            ->with(['personalInterests.interest', 'personalInterests.documents'])
            ->get();

        $query = "SELECT personal_details.* 
                  FROM personal_details 
                  JOIN personal_interests ON personal_details.id = personal_interests.personal_detail_id 
                  JOIN interests ON personal_interests.interest_id = interests.id 
                  JOIN documents ON personal_interests.id = documents.personal_interest_id 
                  WHERE interests.name = 'Animals' 
                  GROUP BY personal_details.id 
                  HAVING COUNT(documents.id) = 1";

        return view('complex-query.results', [
            'results' => $results,
            'query' => $query,
            'title' => 'Animal Lovers with only 1 document linked'
        ]);
    }

    public function query2()
    {
        // Children & Sport Lovers
        $results = PersonalDetail::whereHas('personalInterests.interest', function($q) {
            $q->whereIn('name', ['Children', 'Sport']);
        })
        ->with(['personalInterests' => function($q) {
            $q->whereHas('interest', function($q) {
                $q->whereIn('name', ['Children', 'Sport']);
            })->with('interest');
        }])
        ->get()
        ->filter(function($person) {
            $hasChildren = $person->personalInterests->contains(function($pi) {
                return $pi->interest->name === 'Children';
            });
            $hasSport = $person->personalInterests->contains(function($pi) {
                return $pi->interest->name === 'Sport';
            });
            return $hasChildren && $hasSport;
        });

        $query = "SELECT pd.* 
                  FROM personal_details pd
                  WHERE EXISTS (
                      SELECT 1 FROM personal_interests pi
                      JOIN interests i ON pi.interest_id = i.id
                      WHERE pi.personal_detail_id = pd.id AND i.name = 'Children'
                  ) AND EXISTS (
                      SELECT 1 FROM personal_interests pi
                      JOIN interests i ON pi.interest_id = i.id
                      WHERE pi.personal_detail_id = pd.id AND i.name = 'Sport'
                  )";

        return view('complex-query.results', [
            'results' => $results,
            'query' => $query,
            'title' => 'Children & Sport Lovers'
        ]);
    }

    public function query3()
    {
        // Unique Interests and the count of people without documents linked to their interests
        $results = Interest::leftJoin('personal_interests', 'interests.id', '=', 'personal_interests.interest_id')
            ->leftJoin('personal_details', 'personal_interests.personal_detail_id', '=', 'personal_details.id')
            ->leftJoin('documents', 'personal_interests.id', '=', 'documents.personal_interest_id')
            ->select('interests.name', DB::raw('COUNT(DISTINCT personal_details.id) as person_count'))
            ->whereNull('documents.id')
            ->groupBy('interests.id', 'interests.name')
            ->get();

        $query = "SELECT interests.name, COUNT(DISTINCT personal_details.id) as person_count
                  FROM interests
                  LEFT JOIN personal_interests ON interests.id = personal_interests.interest_id
                  LEFT JOIN personal_details ON personal_interests.personal_detail_id = personal_details.id
                  LEFT JOIN documents ON personal_interests.id = documents.personal_interest_id
                  WHERE documents.id IS NULL
                  GROUP BY interests.id, interests.name";

        return view('complex-query.results', [
            'results' => $results,
            'query' => $query,
            'title' => 'Unique Interests and count of people without documents'
        ]);
    }

    public function query4()
    {
        // People with 5 or 6 interests with at least one of the interests having multiple documents
        $results = PersonalDetail::select('personal_details.*')
            ->join('personal_interests', 'personal_details.id', '=', 'personal_interests.personal_detail_id')
            ->join('interests', 'personal_interests.interest_id', '=', 'interests.id')
            ->leftJoin('documents', 'personal_interests.id', '=', 'documents.personal_interest_id')
            ->groupBy('personal_details.id')
            ->havingRaw('COUNT(DISTINCT personal_interests.interest_id) BETWEEN 5 AND 6')
            ->havingRaw('SUM(CASE WHEN documents.id IS NOT NULL THEN 1 ELSE 0 END) >= 1')
            ->with(['personalInterests.interest', 'personalInterests.documents'])
            ->get();

        $query = "SELECT personal_details.*
                  FROM personal_details
                  JOIN personal_interests ON personal_details.id = personal_interests.personal_detail_id
                  JOIN interests ON personal_interests.interest_id = interests.id
                  LEFT JOIN documents ON personal_interests.id = documents.personal_interest_id
                  GROUP BY personal_details.id
                  HAVING COUNT(DISTINCT personal_interests.interest_id) BETWEEN 5 AND 6
                  AND SUM(CASE WHEN documents.id IS NOT NULL THEN 1 ELSE 0 END) >= 1";

        return view('complex-query.results', [
            'results' => $results,
            'query' => $query,
            'title' => 'People with 5-6 interests with at least one interest having documents'
        ]);
    }
}