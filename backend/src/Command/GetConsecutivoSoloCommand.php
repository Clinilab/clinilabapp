<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Controller\ConsecutivoController;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrai;
use App\Entity\Consecutivo;
use DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;

class GetConsecutivoSoloCommand extends Command
{
    public $entityManager;
    public $conseController;
    public $validator;
    public $register;

    
    public function __construct(EntityManagerInterface $entityManager,ConsecutivoController $conse,ValidatorInterface $validator,ManagerRegistry $register)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->conseController = $conse;
        $this->validator = $validator;
        $this->register = $register;

    }


    protected static $defaultName = 'getConsecutivoSoloCommand';
    protected static $defaultDescription = 'Reinicia el consecutivo diario sin actualizar el consecutivo';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->entityManager; 
        $validator = $this->validator; 
        $register= $this->register; 

        $fechActual =  date('y-m-d');
        $repository = $entityManager->getRepository(Consecutivo::class);
        $registros = $repository->findByActivo(true);
        $fechaAnterior = $registros[0]->fechaactual;
        $fechaAnterior =  $fechaAnterior->format('y-m-d');

        if ($fechaAnterior != $fechActual) {
            $fecha = DateTime::createFromFormat('y-m-d', $fechaAnterior);
            $fecha->modify('+1 day');
            $nuevaFecha = $fecha->format('y-m-d');           

            $mesanterior = date('m', strtotime($fechaAnterior));
            $mesatual = date('m', strtotime($fechActual));  

            $em = $register->getManager();
            $repository = $entityManager->getRepository(Consecutivo::class);
            $consecutivo = $repository->findOneBy(['id' => 1]);         
            $consecutivo->setFechaactual(new \DateTime($nuevaFecha));                       
            //Consecutivo diario sin reinicar el prefijo
            $consecutivo->setConsecutivo(0);
            
             $em->flush();
            $output->writeln("Consecutivo automático mes: " . $mesanterior.'-'.$mesatual );
        }
        return 0;
    }
}

