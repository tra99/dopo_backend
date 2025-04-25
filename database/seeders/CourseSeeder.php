<?php


namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        Course::create(attributes: [
            'title' => 'គណិតវិទ្យាអនុវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(3),
            'description' => 'សិក្សាអំពីគណិតវិទ្យាពីថ្នាក់ទី​​៧-៩ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://infinitylearn.com/surge/wp-content/uploads/2021/12/MicrosoftTeams-image-58.jpg',
        ]);
        Course::create([
            'title' => 'គណិតវិទ្យាវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(6),
            'description' => 'សិក្សាអំពីគណិតវិទ្យាពីថ្នាក់ទី​​១០-១២ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://www.templeton.org/wp-content/uploads/2023/08/Mathematics-Article-BANNER-1-scaled.jpg',
        ]);
        Course::create(attributes: [
            'title' => 'រូបវិទ្យាអនុវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(3),
            'description' => 'សិក្សាអំពីរូបវិទ្យាពីថ្នាក់ទី​​៧-៩ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://static.wixstatic.com/media/c47c84_d4a2b8d8ee914df090e89774101f1672~mv2.webp/v1/fill/w_600,h_620,al_c,q_85,enc_avif,quality_auto/c47c84_d4a2b8d8ee914df090e89774101f1672~mv2.webp'
        ]);
        Course::create([
            'title' => 'រូបវិទ្យាវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(6),
            'description' => 'សិក្សាអំពីរូបវិទ្យាពីថ្នាក់ទី​​១០-១២ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://images.shiksha.com/mediadata/images/articles/1628666138phpFXnn99.jpeg',
        ]);
        Course::create(attributes: [
            'title' => 'គីមីវិទ្យាអនុវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(3),
            'description' => 'សិក្សាអំពីគីមីវិទ្យាពីថ្នាក់ទី​​៧-៩ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://cdn.vectorstock.com/i/1000v/94/66/subject-of-chemistry-vector-1239466.jpg'
        ]);
        Course::create([
            'title' => 'គីមីវិទ្យាវិទ្យាល័យ',
            'teacher_id' => 4,
            'completion_date' => now()->addMonths(6),
            'description' => 'សិក្សាអំពីគីមីវិទ្យាពីថ្នាក់ទី​​១០-១២ ពីកម្រិតមូលដ្ឋានដល់មធ្យម',
            'image' => 'https://c8.alamy.com/comp/HC7E9E/subject-of-chemistry-HC7E9E.jpg'
        ]);
        Course::create([
            'title' => 'C++ Programming Language',
            'teacher_id' => 3,
            'completion_date' => now()->addMonths(6),
            'description' => 'Learn the fundamentals of C++ programming language',
            'image' => 'https://app.simpleshiksha.com/subject/avatar_1692432875981.webp'
        ]);
        Course::create([
            'title' => 'C Programming Language',
            'teacher_id' => 3,
            'completion_date' => now()->addMonths(6),
            'description' => 'Learn the fundamentals of C programming language',
            'image' => 'https://media.geeksforgeeks.org/wp-content/cdn-uploads/20230630120259/C-Language-Introduction.png'
        ]);
    }
}
