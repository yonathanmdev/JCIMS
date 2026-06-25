<?php

namespace App\Helpers;

class EthiopianDateHelper {

    public static function toEthCalendar($day, $month, $year) {
        // Your logic starts here
        $gregday = (int)$day;
        $gregmonth = (int)$month;
        $gregyear = (int)$year;
        $ethyear=0;
 $ethmonth=0;		
 $ethday=0;
 $ethleapEffect=0;
 $ethleapEffect2 = 0;
  if (((($gregyear-9)+5500) % 4) == 3)
  {
	    $ethleapEffect=1;
  } 
  else
  {
      $ethleapEffect=0;
  }
  if ($gregmonth == 1) {
      $gregmonth = 0;
      if ($gregmonth == 0)//jan
      {
		 $ethyear = $gregyear - 8;
		 if ($gregday <= (8 + $ethleapEffect))
		 {
		     $ethmonth = $gregmonth + 4; //tahissas
		     $ethday = ($gregday + 22 - $ethleapEffect);
		     
		 }
		 else {
		     $ethmonth = $gregmonth + 5; //thir
		     if ($ethleapEffect == 1)
		     {
			 $ethday = $gregday - 9;
		     }
		     else
		     {
			 $ethday = $gregday - 8;
		     }
		     }
	     }
  }
  else if ($gregmonth == 2) 
        {
		  $gregmonth = 1;
		 if ($gregmonth == 1)//feb
		 {
		    $ethyear = $gregyear - 8;
		    if ($gregday <= (7 + $ethleapEffect)) 
            {
			$ethmonth = $gregmonth + 4; //thir
			$ethday = ($gregday + 23 - $ethleapEffect);
			}
		    else {
		        $ethmonth = $gregmonth + 5; //yekatit
			if ($ethleapEffect == 1)
			{
			    $ethday = $gregday - 8;
			}
			else 
			{                  
			    $ethday = $gregday- 7;
			}
			 }
		 }
	  }
	  else if ($gregmonth == 3) {
		 $gregmonth = 2;
		 if ($gregmonth == 2)//mar
		 {		     
		     $ethyear = $gregyear - 8;
		     if ($gregday <= 9) {
			 $ethmonth = $gregmonth + 4; //yekatit
			 $ethday = ($gregday + 21);
			 }
		     else {
			 $ethmonth = $gregmonth + 5; //megabit
			 $ethday = $gregday - 9;
			 }
		 }
	 }
	
		 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 4) {
		 $gregmonth= 3;
		 if ($gregmonth == 3)//apr
		 {
			 $ethyear = $gregyear - 8;
			 if ($gregday <= 8) {
				$ethmonth = $gregmonth + 4; //megabit
				 $ethday = ($gregday + 22);
			 }
			 else {
				 $ethmonth = $gregmonth + 5; //miyaziya
				 $ethday = $gregday - 8;
			 }
			
		 }
	 }
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 5) {
		 $gregmonth = 4;
		 if ($gregmonth == 4)//may
		 {
			 $ethyear = $gregyear - 8;
			 if ($gregday <= 8) {
				 $ethmonth = $gregmonth + 4; //miyaziya
				 $ethday = ($gregday + 22);
			 }
			 else {
				 $ethmonth = $gregmonth + 5; //ginbot
				 $ethday = $gregday - 8;
			 }
			
		 }
	 }
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 6) {
		 $gregmonth = 5;
		 if ($gregmonth == 5)//jun
		 {
			 $ethyear = $gregyear - 8;
			 if ($gregday <= 7) {
				 $ethmonth = $gregmonth + 4; //ginbot
				 $ethday = ($gregday + 23);
			 }
			 else {
				 $ethmonth = $gregmonth + 5; //sene
				 $ethday = $gregday - 7;
			 }
			
		 }
	 } 
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 7) {
		 $gregmonth = 6;
		 if ($gregmonth == 6)//jul
		 {
			 $ethyear = $gregyear - 8;
			 if ($gregday <= 7) {
				 $ethmonth = $gregmonth + 4; //sene
				 $ethday = ($gregday + 23);
			 }
			 else {
				 $ethmonth = $gregmonth + 5; //hamle
				 $ethday = $gregday - 7;
			 }
		 }
	 }
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 8) {
		 $gregmonth = 7;
		 if ($gregmonth == 7)//aug
		 {
			 $ethyear = $gregyear - 8;
			 if ($gregday <= 6) {
				 $ethmonth = $gregmonth + 4; //hamle
				 $ethday = ($gregday + 24);
			 }
			 else {
				 $ethmonth = $gregmonth + 5; //nehasse
				 $ethday = $gregday - 6;
			 }
		 }
	 }
	 
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 9) {
		 $gregmonth = 8;
		 if ($gregmonth == 8)//sep
		 {
			 $ethleapEffect2; // this is not the same leap check as the global peer, rather it checks if the current
			 if (((($gregyear-8)+5500) % 4) == 3)
			 {
			     $ethleapEffect2=1;
			 } 
			 else
			 {
			     $ethleapEffect2=0;
			 }
			 if ($gregday <= 5) {                         //year is leap or not, the global checks if the current-1 is leap or not.
				 $ethyear = $gregyear - 8;
				 $ethmonth = $gregmonth + 4; //nehasse
				 $ethday = ($gregday + 25);
			 }
			 else if ($gregday >= 6 && $gregday <= (10 + $ethleapEffect2)) {
				 $ethyear = $gregyear - 8;
				 $ethmonth = $gregmonth + 5; //Puagme
				 $ethday = $gregday - 5;
			 }
			 else {
				 $ethyear = $gregyear - 7;
				 $ethmonth = $gregmonth - 7; //Meskerem
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday - 11;
				 }
				 else
				 {
					 $ethday = $gregday - 10;
				 }
			 }
		 }
	 }

	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 10)
	 {
		 $gregmonth = 9;
		 if ($gregmonth== 9)//oct
		 {
		 $ethleapEffect2; // this is not the same leap check as the global peer, rather it checks if the current
		 if (((($gregyear-8)+5500) % 4) == 3)
		 {
		     $ethleapEffect2=1;
		 }
		 else
		 {
		     $ethleapEffect2=0;
		 }
		     // check if last ethiopian year is leap or not, bc it affects months after puagme 5 or 6
		     $ethyear = $gregyear - 7;                       // and consider that there is no gc leap arround this month 
                                                                       // so it will continue until it gets it.
			 if ($gregday <= (10 + $ethleapEffect2)) {
				 $ethmonth = $gregmonth - 8;  //meskerem
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday + 19;
				 }
				 else
				 {
					$ethday = $gregday + 20;
				 }
			 }
			 else {
				 $ethmonth = $gregmonth - 7;  //tikimt
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday - 11;
				 }
				 else
				 {
					 $ethday = $gregday - 10;
				 }
			 }
		 }
	 }
  //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 11) {
		 $gregmonth = 10;
		 if ($gregmonth == 10)//nov
		 {
			$ethleapEffect2;                              // this is not the same leap check as the global peer, 
                                                                              //rather it checks if the current
             if (((($gregyear-8)+5500) % 4) == 3)
             {
             $ethleapEffect2 = 1;
             } else
             {
                 $ethleapEffect2 = 0;// check if last ethiopian year is leap or not, 
            }                       //bc it affects months after puagme 5 or 6
			 $ethyear = $gregyear - 7;                           // and consider that there is no gc leap arround this month 
                                                                           //so it will continue until it gets it.
			 if ($gregday <= (9 + $ethleapEffect2)) {
				 $ethmonth = $gregmonth - 8;  //tikimt
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday + 20;
				 }
				 else
				 {
					 $ethday = $gregday + 21;
				 }
			 }
			 else {
				 $ethmonth = $gregmonth - 7;  //hidar
				 if ($ethleapEffect2 == 1)
				 {
					$ethday = $gregday - 10;
				 }
				 else
				 {
					 $ethday = $gregday - 9;
				 }
			 }
		 }
	 }
	 
	 //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 else if ($gregmonth == 12) {
		 $gregmonth = 11;
		 if ($gregmonth == 11)//dec
		 {
			$ethleapEffect2;                              // this is not the same leap check as the global peer, 
                                                                              //rather it checks if the current
            if (((($gregyear-8)+5500) % 4) == 3)
	            {
	                $ethleapEffect2 = 1;
	            } else
	            {
	                $ethleapEffect2 = 0; 
	            }
			 $ethyear = $gregyear - 7;                       // and consider that there is no gc leap arround this month 
                                                                          //so it will continue until it gets it.
			 if ($gregday <= (9 + $ethleapEffect2)) {
				 $ethmonth = $gregmonth - 8;  //hidar
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday + 20;
				 }
				 else
				 {
					 $ethday = $gregday + 21;
				 }
			 }
			 else {
				 $ethmonth = $gregmonth - 7;  //tahissas
				 if ($ethleapEffect2 == 1)
				 {
					 $ethday = $gregday - 10;
				 }
				 else
				 {
					 $ethday = $gregday - 9;
				 }
			 }
		 }
	 } 
     return [
            'day' => $ethday,
            'month' => $ethmonth,
            'year' => $ethyear,
            'full' => "$ethday/$ethmonth/$ethyear"
        ];
    }

    public static function getMonthName($monthNumber) {
        $months = [
            1 => "መስከረም", 2 => "ጥቅምት", 3 => "ኅዳር", 4 => "ታኅሣስ", 
            5 => "ጥር", 6 => "የካቲት", 7 => "መጋቢት", 8 => "ሚያዝያ", 
            9 => "ግንቦት", 10 => "ሰኔ", 11 => "ሐምሌ", 12 => "ነሐሴ", 13 => "ጳጉሜ"
        ];
        return $months[(int)$monthNumber] ?? '';
    }
}