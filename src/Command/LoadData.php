<?php
    /**
     * Created by PhpStorm.
     * User: Ubel
     * Date: 15/02/2021
     * Time: 6:57 PM
     */
    
    namespace App\Command;

    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bridge\Doctrine\IdGenerator\UuidV1Generator;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Console\Style\SymfonyStyle;
    use Symfony\Component\Finder\Finder;
    
    class LoadData extends Command
    {
        /**  @var EntityManagerInterface */
        private $entityManager;

        /**
          * UpdateCedulasCommand constructor.
          *
          * @param EntityManagerInterface $entityManager
          * @param UuidGenerator $uuid
          */
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->finder = new Finder();
            $this->entityManager = $entityManager;
            
            parent::__construct();
        }
        protected function configure()
        {
            $this
                ->setName('update:countries')
                ->setDescription('Carga automaticamente los paises y susrespectivas provincias');
        }

        /**
         * @param InputInterface $input
         * @param OutputInterface $output
         * @return int
         */
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $finder = new Finder();
            $finder->in(__DIR__ . '/Data');
            $finder->name('*.sql');
            $finder->files();
                
            foreach ($finder as $file) {
                print "Importing: {$file->getBasename()} " . PHP_EOL;
    
                $sql = $file->getContents();
    
                $this->entityManager->getConnection()->exec($sql);
    
    
                $this->entityManager->flush();
            }
    
            $this->entityManager->flush();
            return 0;
        }
    }