<?php 
	if (! function_exists('distance')) {
	  function distance($lat1, $lng1, $lat2, $lng2, $unit, $round) {
	    $result = 0;
	    if (($lat1 == $lat2) && ($lng1 == $lng2)) {
	      $result = 0;
	    }
	    else {
	      $theta = $lng1 - $lng2;
	      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	      $dist = acos($dist);
	      $dist = rad2deg($dist);
	      $miles = $dist * 60 * 1.1515;
	      $unit = strtoupper($unit);

	      if ($unit == "K") {
	        $result = ($miles * 1.609344);
	      } else if ($unit == "N") {
	        $result = ($miles * 0.8684);
	      } else {
	        $result = $miles;
	      }
	      
	      $round = intval($round);
	      if ($round && is_numeric($round)) {
	        return round($result, $round);
	      } else {
	        return $result;
	      }
	    }
	  }
}