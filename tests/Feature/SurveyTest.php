<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
class SurveyTest extends TestCase
{
  // use RefreshDatabase;
  public function test_the_application_returns_a_successful_response(): void
  {
    $response = $this->get('/');

    $response->assertStatus(200);
  }

  // Test: create survey successful => survey A (start from 01 March 2025 - 05 March 2025)
  public function test_create_survey_successfully()
  {
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    $data = [
      'title' => 'Survey A',
      'start_date' => '2025-03-01',
      'end_date' => '2025-03-05',
      'status' => 1,
      'school_type' => 'Lower Secondary School'
    ];

    $response = $this->postJson('/api/v1/surveys', $data, [
      'Authorization' => 'Bearer ' . $token
    ]);
    $response->assertStatus(201);

    $this->assertDatabaseHas('surveys', [
      'title' => 'Survey A',
      'start_date' => '2025-03-01',
      'end_date' => '2025-03-05',
      'school_type' => 'Lower Secondary School'
    ]);
  }


  // Test: create 5 questions successful => questions 1 to 5
  public function test_create_5_questions_successfully()
  {
    // Find the user and generate a token
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    // Prepare the questions data
    $questions = [];
    for ($i = 1; $i <= 5; $i++) {
      $questions[] = [
        'category_id' => 3,
        'question' => "Question $i",
        'description' => "Description for question $i",
        'question_type' => 'radio',
        'school_type' => 'Lower Secondary School',
        'answer_option' => [
          ['title' => "Option 1", 'point' => $i * 10],
          ['title' => "Option 2", 'point' => $i * 10],
        ],
        'published_at' => '2025-03-01'
      ];
    }

    // Send the data to the API to create the questions
    $response = $this->postJson('/api/v1/questions', $questions, [
      'Authorization' => 'Bearer ' . $token
    ]);

    // Assert that the response status is 201 Created
    $response->assertStatus(201);

    // Check if the correct questions are returned
    foreach ($questions as $index => $questionData) {
      $response->assertJsonFragment([
        'question' => $questionData['question'],
        'school_type' => $questionData['school_type'],
      ]);
    }

    // Verify that the questions were saved in the database
    foreach ($questions as $questionData) {
      $this->assertDatabaseHas('questions', [
        'question' => $questionData['question'],
      ]);
    }
  }


  // Test: publish the 10 questions at 25 February 2025
  public function test_publish_10_questions_successfully()
  {
    // Step 1: Find the user and generate a token
    $user = User::find(1);  // You can change this to the appropriate user if needed
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];  // Extract the actual token

    // Fetch the first 10 questions from the database
    $questions = Question::limit(10)->get();

    // Prepare the updated data for each question
    foreach ($questions as $question) {

      $update = ['published_at' => '2025-02-25'];

      // Send the PUT request to the API to update the question
      $response = $this->patchJson('/api/v1/questions/' . $question->id, $update, [
        'Authorization' => 'Bearer ' . $token
      ]);

      // Step 5: Assert that the response status is 200 (OK)
      $response->assertStatus(200);

    }

    //verify that the questions were saved in the database with the correct `published_at` date
    foreach ($questions as $question) {
      $this->assertDatabaseHas('questions', [
        'id' => $question->id,
        'published_at' => '2025-02-25',
      ]);
    }
  }

  // Test: get questions of survey A => return questions 1 to 5 because the survey start at 01 March 2025 and the questions are published before that
  public function test_get_questions_of_survey_a_successfully()
  {
    // Find the user and generate a token
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];  // Extract the actual token

    // Get the latest Survey (assuming the most recent survey is the target)
    $survey = Survey::orderBy('created_at', 'desc')->first();

    // Fetch questions published on the same date as the survey's start date
    $questions = Question::whereBetween('published_at', [$survey->start_date, $survey->end_date])  // Filter questions between start_date and end_date
      ->get();

    // Send the GET request to retrieve questions for Survey A
    $response = $this->getJson('/api/v1/surveys/' . $survey->id, [
      'Authorization' => 'Bearer ' . $token
    ]);

    // Assert that the response status is 200 (OK)
    $response->assertStatus(200);
    $response->assertJsonCount(5, 'survey.questions');

    // Verify that the correct questions are returned
    foreach ($questions as $question) {
      $response->assertJsonFragment([
        'question' => $question->question,
        'published_at' => $question->published_at,
      ]);
    }
  }

  // Test: answer to the 5 questions successful
  public function test_answer_to_the_5_questions_successfully()
  {
    // Find the user and generate a token
    $user = User::find(1);  // You can change this to the appropriate user if needed
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];  // Extract the actual token

    // Fetch the first 5 questions
    $questions = Question::orderBy('created_at', 'desc')->limit(5)->get();

    // Prepare the answers for the 5 questions
    $answers = [];
    foreach ($questions as $question) {
      $answers[] = [
        "question_id" => $question->id,
        "survey_id" => 1,
        "answers" => [
          ["title" => "Option 1", "point" => 20]
        ]
      ];
    }

    // Send the answers to the API
    $response = $this->postJson('/api/v1/answers', $answers, [
      'Authorization' => 'Bearer ' . $token
    ]);

    // Assert that the response status is 201 (Created)
    $response->assertStatus(201);
  }


  // Test: get answer of each questions
  public function test_get_answer_of_each_questions()
  {
    // Find the user and generate a token
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];



    // Send the answers to the API
    $response = $this->get('/api/v1/answers', [
      'Authorization' => 'Bearer ' . $token
    ]);

    // Assert that the response status is 201 (Created)
    $response->assertStatus(200);
  }

  // Test: get answers of survey A
  public function test_get_answers_of_survey_a()
  {
    $user = User::find(1);  // You can change this to the appropriate user if needed
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    $surveyId = 1;
    $response = $this->getJson('/api/v1/answers?by_survey_id=' . $surveyId, [
      'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing([
      'error' => 'Answer not found.'
    ]);

    $response->assertJsonStructure([
      'data' => [
        '*' => [ // Assuming the response contains an array of answers in the 'data' key
          'id',
          'question_id',
          'survey_id',
          'answer',
          'point',
          'created_at',
          'updated_at'
        ]
      ]
    ]);
  }

  // Test: delete survey A fail => because survey A has answers so it can't be deleted
  public function test_delete_survey_a_fail()
  {
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    $surveyId = 1;
    $response = $this->delete('/api/v1/surveys/' . $surveyId, [], [
      'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertStatus(400);
    $response->assertJson([
      'error' => 'Cannot delete survey because it has associated answers.'
    ]);
  }

  // Test: delete 5 questions fail => because the last 5 questions has answers so it can't be deleted
  public function test_delete_last_5_questions_fail()
  {
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];


    $questions = Question::orderBy('created_at', 'desc')->limit(5)->get();
    foreach ($questions as $question) {
      $response = $this->delete('/api/v1/questions/' . $question->id, [], [
        'Authorization' => 'Bearer ' . $token
      ]);
      $response->assertStatus(400);
      $response->assertJson([
        'error' => 'មិនអាចលុបសំណួរណាដែលមានចម្លើយបានទេ!'
      ]);
    }
  }

  // Test: delete all answers of survey A successful
  public function test_delete_all_answers_of_survey_a_successfully()
  {
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    $surveyId = 1;
    $answers = Answer::where('survey_id', $surveyId)->get();



    foreach ($answers as $answer) {
      $response = $this->delete('/api/v1/answers/' . $answer->id, [], [
        'Authorization' => 'Bearer ' . $token
      ]);
      $response->assertStatus(200);
      $response->assertJson([
        'message' => 'Answer deleted successfully!'
      ]);
    }
  }


  // Test: delete survey A again, and it is successful => because no answers are attached to survey A
  public function test_delete_survey_a_again_and_it_is_successful()
  {
    $user = User::find(1);
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;
    $token = explode('|', $token)[1];

    $surveyId = 1;
    $response = $this->delete('/api/v1/surveys/' . $surveyId, [], [
      'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertStatus(200);
    $response->assertJson([
      'message' => 'Survey deleted successfully'
    ]);
  }

}
