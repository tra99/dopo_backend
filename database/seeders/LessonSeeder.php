<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        // C++ programming language
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Introduction to C++',
            'description' => 'Overview of C++ history, features, and setup (compilers, IDEs). Writing a basic "Hello, World!" program.',
            'link_video' => 'https://youtu.be/s0g4ty29Xgg?si=3tj_f6GrXlrxdfVd',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Basic Syntax & Data Types',
            'description' => 'Variables, constants, primitive data types, operators, and basic I/O (cin, cout).',
            'link_video' => 'https://youtu.be/irqbmMNs2Bo',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Control Structures',
            'description' => 'Conditional statements (if, switch), loops (for, while), and flow control (break, continue).',
            'link_video' => 'https://youtu.be/RP3BWoep69U?si=MfZq2VAwLdNR8J1h',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Functions',
            'description' => 'Function declaration, parameters, return types, pass-by-reference, overloading, and recursion.',
            'link_video' => 'https://youtu.be/ZSPZob_1TOk',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Arrays & Strings',
            'description' => 'Fixed-size arrays, multi-dimensional arrays, C-style strings, and C++ std::string operations.',
            'link_video' => 'https://youtu.be/QFrJQq6Iox8?si=HoD5C6bybxFL-77h',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Pointers & Memory Management',
            'description' => 'Pointers, references, dynamic memory allocation (new, delete), and common pitfalls.',
            'link_video' => 'https://youtu.be/slzcWKWCMBg?si=jjcxV9ifAY3UiGIt',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Object-Oriented Programming (OOP)',
            'description' => 'Classes, objects, constructors/destructors, inheritance, polymorphism, and encapsulation.',
            'link_video' => 'https://youtu.be/_8H2n0nDfd4?si=rdXyCI7fh8uEg7vI',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Standard Template Library (STL) Basics',
            'description' => 'Containers (vector, map), iterators, and basic algorithms (sort, find).',
            'link_video' => 'https://youtu.be/_NlRcT5gWpo?si=pfrjnp7J1oGV2i06',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'File Handling',
            'description' => 'Reading/writing files using fstream, text vs. binary modes, and error handling.',
            'link_video' => 'https://youtu.be/EaHFhms_Shw?si=Yn09W_oGGL-jsuP9',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 7, // Assuming course with id 2 exists
            'title' => 'Error Handling & Debugging',
            'description' => 'Exceptions (try, catch), common bugs, debugging techniques, and best practices.',
            'link_video' => 'https://youtu.be/kjEhqgmEiWY?si=yNVYvYaQ6EY8xI3U',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);

        // C programming language
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Introduction to C',
            'description' => 'History, features, and importance of C. Setting up a C environment (compilers like GCC, IDEs).',
            'link_video' => 'https://youtu.be/gEJBFKDkqTE?si=PyFBH4vVUJTKe_Yq',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Basic Syntax & Data Types',
            'description' => 'Variables, constants, primitive data types (int, float, char, etc.), and basic I/O (printf, scanf).',
            'link_video' => 'https://youtu.be/sARaqR0hRI4?si=B9QXD7qT_4IRxCuO',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Operators & Expressions',
            'description' => 'Arithmetic, relational, logical, bitwise, and assignment operators. Operator precedence and expressions.',
            'link_video' => 'https://youtu.be/_57FcSBtJNU?si=q6_AFRn2nCmFPypL',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Control Flow',
            'description' => 'Conditional statements (if, else, switch), loops (for, while, do-while), and jumps (break, continue, goto).',
            'link_video' => 'https://youtu.be/K8mntKyBJGc?si=EDE-p5BFRLQBm-_z',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Functions & Scope',
            'description' => 'Defining functions, parameters, return values, recursion, and variable scope (local vs. global).',
            'link_video' => 'https://youtu.be/ej-GOnj7mj0?si=fiSlf07zc5MGUnpw',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Arrays & Strings',
            'description' => 'Single and multi-dimensional arrays, C-style strings (character arrays), and string manipulation functions (strcpy, strlen, etc.).',
            'link_video' => 'https://youtu.be/MOeGnamlUP4?si=RShC0dzPeqYpq7An',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Pointers & Memory Management',
            'description' => 'Pointer basics, pointer arithmetic, dynamic memory allocation (malloc, calloc, free), and common pitfalls (dangling pointers, leaks).',
            'link_video' => 'https://youtu.be/2ybLD6_2gKM?si=NKCOnl-T8CTX6KzF',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Structures & Unions',
            'description' => 'Defining structs and unions, accessing members, nested structures, and typedef.',
            'link_video' => 'https://youtu.be/oKXP1HZ8xIs?si=YvKevaGHBdAqSiIL',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'File Handling',
            'description' => 'Reading/writing files (fopen, fclose, fprintf, fscanf), text vs. binary modes, and error handling.',
            'link_video' => 'https://youtu.be/MQIF-WMUOL8?si=dYMIciaUmTOERls6',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 8, // Assuming course with id 2 exists
            'title' => 'Preprocessor & Debugging',
            'description' => 'Macros (#define), conditional compilation (#ifdef), header files, and debugging techniques (using gdb).',
            'link_video' => 'https://youtu.be/_w2OBtOvzTg?si=ogu8NllnMYFHmK1x',
            'end_date' => now()->addMonths(3),
            'quiz_total_score' => 10
        ]);
    }
}
