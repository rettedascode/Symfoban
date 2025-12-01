<?php

namespace App\DataFixtures;

use App\Entity\Board;
use App\Entity\BoardTemplate;
use App\Entity\Column;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DemoBoardFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create demo users
        $admin = new User();
        $admin->setEmail('admin@demo.com');
        $admin->setName('Admin User');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('alice@demo.com');
        $user1->setName('Alice Johnson');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'user123'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('bob@demo.com');
        $user2->setName('Bob Smith');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'user123'));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('charlie@demo.com');
        $user3->setName('Charlie Brown');
        $user3->setRoles(['ROLE_USER']);
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'user123'));
        $manager->persist($user3);

        // Create tags
        $tags = [];
        $tagData = [
            ['name' => 'Frontend', 'color' => '#3B82F6'],
            ['name' => 'Backend', 'color' => '#10B981'],
            ['name' => 'Bug', 'color' => '#EF4444'],
            ['name' => 'Feature', 'color' => '#8B5CF6'],
            ['name' => 'Urgent', 'color' => '#F59E0B'],
            ['name' => 'Documentation', 'color' => '#6366F1'],
        ];

        foreach ($tagData as $tagInfo) {
            $tag = new Tag();
            $tag->setName($tagInfo['name']);
            $tag->setColor($tagInfo['color']);
            $manager->persist($tag);
            $tags[$tagInfo['name']] = $tag;
        }

        // Create a board template
        $template = new BoardTemplate();
        $template->setName('Software Development');
        $template->setDescription('Standard workflow for software development projects');
        $template->setColumns(['Backlog', 'To Do', 'In Progress', 'Review', 'Done']);
        $manager->persist($template);

        // Create demo board
        $board = new Board();
        $board->setName('Symfoban Development Board');
        $manager->persist($board);

        // Create columns
        $columns = [];
        $columnNames = ['Backlog', 'To Do', 'In Progress', 'Review', 'Done'];
        foreach ($columnNames as $index => $columnName) {
            $column = new Column();
            $column->setName($columnName);
            $column->setPosition($index);
            $column->setBoard($board);
            $manager->persist($column);
            $columns[$columnName] = $column;
        }

        // Create tasks with various properties
        $tasks = [
            [
                'title' => 'Implement user authentication',
                'description' => 'Add login and registration functionality with Symfony Security',
                'column' => 'Done',
                'position' => 0,
                'priority' => 'high',
                'dueDate' => new \DateTime('2024-11-15'),
                'createdBy' => $admin,
                'assignedTo' => $user1,
                'tags' => ['Backend', 'Feature'],
            ],
            [
                'title' => 'Create Kanban board UI',
                'description' => 'Design and implement the drag-and-drop Kanban interface',
                'column' => 'Done',
                'position' => 1,
                'priority' => 'critical',
                'dueDate' => new \DateTime('2024-11-20'),
                'createdBy' => $admin,
                'assignedTo' => $user1,
                'tags' => ['Frontend', 'Feature'],
            ],
            [
                'title' => 'Add task priorities',
                'description' => 'Implement priority levels with visual indicators',
                'column' => 'Done',
                'position' => 2,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2024-11-25'),
                'createdBy' => $user1,
                'assignedTo' => $user2,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Fix dark mode toggle',
                'description' => 'Resolve issue with dark mode toggle not working correctly',
                'column' => 'Done',
                'position' => 3,
                'priority' => 'low',
                'dueDate' => new \DateTime('2024-11-28'),
                'createdBy' => $user2,
                'assignedTo' => $user1,
                'tags' => ['Frontend', 'Bug'],
            ],
            [
                'title' => 'Implement search functionality',
                'description' => 'Add search for tasks and boards with filtering options',
                'column' => 'Done',
                'position' => 4,
                'priority' => 'high',
                'dueDate' => new \DateTime('2024-12-01'),
                'createdBy' => $admin,
                'assignedTo' => $user2,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Add activity logging',
                'description' => 'Track all user actions and changes in the system',
                'column' => 'Review',
                'position' => 0,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2024-12-05'),
                'createdBy' => $admin,
                'assignedTo' => $user3,
                'tags' => ['Backend', 'Feature'],
            ],
            [
                'title' => 'Create API documentation',
                'description' => 'Write comprehensive API documentation for all endpoints',
                'column' => 'In Progress',
                'position' => 0,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2024-12-10'),
                'createdBy' => $admin,
                'assignedTo' => $user1,
                'tags' => ['Documentation'],
            ],
            [
                'title' => 'Optimize database queries',
                'description' => 'Review and optimize slow database queries for better performance',
                'column' => 'In Progress',
                'position' => 1,
                'priority' => 'high',
                'dueDate' => new \DateTime('2024-12-08'),
                'createdBy' => $user2,
                'assignedTo' => $user2,
                'tags' => ['Backend', 'Urgent'],
            ],
            [
                'title' => 'Add email notifications',
                'description' => 'Send email notifications when tasks are assigned or updated',
                'column' => 'To Do',
                'position' => 0,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2024-12-15'),
                'createdBy' => $admin,
                'assignedTo' => $user3,
                'tags' => ['Backend', 'Feature'],
            ],
            [
                'title' => 'Implement task comments',
                'description' => 'Allow users to add comments and discussions to tasks',
                'column' => 'To Do',
                'position' => 1,
                'priority' => 'low',
                'dueDate' => new \DateTime('2024-12-20'),
                'createdBy' => $user1,
                'assignedTo' => null,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Fix mobile responsiveness',
                'description' => 'Improve mobile experience for Kanban board on small screens',
                'column' => 'To Do',
                'position' => 2,
                'priority' => 'high',
                'dueDate' => new \DateTime('2024-12-12'),
                'createdBy' => $user2,
                'assignedTo' => $user1,
                'tags' => ['Frontend', 'Bug'],
            ],
            [
                'title' => 'Add export functionality',
                'description' => 'Allow users to export boards and tasks to CSV/PDF',
                'column' => 'Backlog',
                'position' => 0,
                'priority' => 'low',
                'dueDate' => new \DateTime('2025-01-15'),
                'createdBy' => $admin,
                'assignedTo' => null,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Implement time tracking',
                'description' => 'Add ability to track time spent on tasks',
                'column' => 'Backlog',
                'position' => 1,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2025-01-20'),
                'createdBy' => $user3,
                'assignedTo' => null,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Add task attachments',
                'description' => 'Allow users to attach files to tasks',
                'column' => 'Backlog',
                'position' => 2,
                'priority' => 'medium',
                'dueDate' => new \DateTime('2025-01-25'),
                'createdBy' => $admin,
                'assignedTo' => null,
                'tags' => ['Feature'],
            ],
            [
                'title' => 'Create user dashboard',
                'description' => 'Build personalized dashboard showing user tasks and statistics',
                'column' => 'Backlog',
                'position' => 3,
                'priority' => 'high',
                'dueDate' => new \DateTime('2025-02-01'),
                'createdBy' => $admin,
                'assignedTo' => null,
                'tags' => ['Frontend', 'Feature'],
            ],
        ];

        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setDescription($taskData['description']);
            $task->setColumn($columns[$taskData['column']]);
            $task->setPosition($taskData['position']);
            $task->setPriority($taskData['priority']);
            $task->setDueDate($taskData['dueDate']);
            $task->setCreatedBy($taskData['createdBy']);
            if ($taskData['assignedTo']) {
                $task->setAssignedTo($taskData['assignedTo']);
            }

            // Add tags
            foreach ($taskData['tags'] as $tagName) {
                if (isset($tags[$tagName])) {
                    $task->addTag($tags[$tagName]);
                }
            }

            $manager->persist($task);
        }

        // Create another board template
        $template2 = new BoardTemplate();
        $template2->setName('Marketing Campaign');
        $template2->setDescription('Template for managing marketing campaigns');
        $template2->setColumns(['Ideas', 'Planning', 'In Production', 'Review', 'Published']);
        $manager->persist($template2);

        $manager->flush();
    }
}

