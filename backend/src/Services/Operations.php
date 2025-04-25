<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;

class Operations
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function randomString($length=6,$uc=TRUE,$n=TRUE,$sc=FALSE) {
        $source = 'abcdefghijklmnopqrstuvwxyz';
        if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($n==1) $source .= '1234567890';
        if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
        if($length>0){
            $rstr = "";
            $source = str_split($source,1);
            for($i=1; $i<=$length; $i++){
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,count($source));
                $rstr .= $source[$num-1];
            }
     
        }
        return $rstr;
    }

    public function convertDateTime($fecha, $tipo = null){
        $fechaArray = explode("/", $fecha);

        switch ($tipo) {
            case 1:
            $fecha = (new \DateTime($fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0].' 00:00:00'));
            break;

            case 2:
            $fecha = (new \DateTime($fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0].' 23:59:00'));
            break;

            default:
            $fecha = (new \DateTime($fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0]));
            break;
        }

        return $fecha; 
    }

    public function calculateAge($fechaNacimiento){
	    $fechaActual = new \DateTime();
	    $edad = $fechaActual->diff($fechaNacimiento);

	    return $edad->y;
	}

    /**
    * Truncate a float number, example: <code>truncate(-1.49999, 2); // returns -1.49
    * truncate(.49999, 3); // returns 0.499
    * </code>
    * @param float $val Float number to be truncate
    * @param int f Number of precision
    * @return float
    */
    public function truncate($val, $f="0")
    {
        if(($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }

        return $val;
    }
}