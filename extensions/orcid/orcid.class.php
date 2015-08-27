<?php

class OrcidHelper
{

    public static function validateOrcid($orcid)
    {
        //get orcid digits
        $orcid = str_split(str_replace("-", "", $orcid));
        if(count($orcid) == 16)
        {
            $total = 0;
            for($i = 0; $i < 15; $i++) {
                $total = ($total + $orcid[$i]) * 2;
            }
            $remainder = $total % 11;
            $result = (12 - $remainder) % 11;            
            if($result == 10)
                $result = "X";

            if($result == $orcid[15])
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public static function getDetails($orcid)
    {
        $url = "http://pub.orcid.org/v1.2/$orcid/orcid-bio";
                      
        $bio = simplexml_load_string(CurlHelper::get($url)); 
                                      
        if(!$bio->{'error-desc'})
        {
            $given = (string)$bio->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'given-names'};
            $family = (string)$bio->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'family-name'};                    
        }
                        
        return array("given" => $given, "family" => $family);
    }

}

?>