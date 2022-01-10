<?php
declare(strict_types=1);
namespace App\Command\User;


use App\Model\User\Entity\User\Role;
use App\Model\User\UseCase\User\Role\Handler;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleCommand extends Command
{

    private $user;
    private $validator;
    private $handler;

    public function __construct(UserFetcher $fetcher, ValidatorInterface $validator, Handler $handler)
    {
        $this->user = $fetcher;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('user:role')->setDescription('Changes user role');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->user->findByEmail($email)){
            throw new LogicException('User is not found.');
        }

        $command = new \App\Model\User\UseCase\User\Role\Command($user->id);

        $roles = [Role::ADMIN, Role::USER];
        $command->role = $helper->ask($input, $output, new ChoiceQuestion('Email: ', $roles,0));

        $violations = $this->validator->validate($command);

        if ($violations->count()){
            foreach ($violations as $violation){
                $output->writeln("<error>{$violation->getPropertyPath()}:{$violation->getMessage()}</error>");
            }
            return;
        }

        $this->handler->handle($command);
        $output->writeln('<info>Done!</info>');
    }

}