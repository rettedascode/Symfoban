<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Email address')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Full name')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email');
        $name = $input->getOption('name');
        $password = $input->getOption('password');

        $helper = $this->getHelper('question');

        if (!$email) {
            $question = new Question('Enter email address: ');
            $email = $helper->ask($input, $output, $question);
        }

        if (!$name) {
            $question = new Question('Enter full name: ');
            $name = $helper->ask($input, $output, $question);
        }

        if (!$password) {
            $question = new Question('Enter password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);
        }

        if ($this->userRepository->findOneBy(['email' => $email])) {
            $io->error('User with this email already exists!');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Admin user "%s" created successfully!', $email));

        return Command::SUCCESS;
    }
}

