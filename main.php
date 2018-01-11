<section>
        <table class="table is-hoverable is-stripped middle">
             <thead>
                <tr>
                    <th width="200px">Name</th>
                    <th width="350px">Availability (.com)</th>
                    <th width="100px">Check</th>                             
                    <th width="150px">Twitter</th>                    
                </tr>
            </thead>

            <tbody>        
                <?php
                if( isset($_GET["keywords"])) {
                    $lcase = preg_replace("/[^a-zA-Z0-9\s]/", "", strtolower($_GET["keywords"]));
                    $keywords = explode(" ",$lcase);
                    $collect = array();
                    depth_picker($keywords, "", $collect);
                    for($i=0; $i<count($collect); $i++) {
                        $name = $collect[$i];
                        if($name == "") continue;
                        $domain = preg_replace("/[^a-zA-Z0-9]/", "", $name);
                        $jresponse = domaincheck($domain);
                        if($jresponse["available"]==1) $avail = "Yes";
                        else $avail = "No";
                        $twitter = twitterAccountExists($domain);
                        ?>
                        <tr>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $avail.' ('.$domain.'.com)';?></td>
                            <td><a target="_blank" href="<?php 
                                                            if($avail=="No"){echo 'https://www.whois.com/whois/'.$domain.'.com'; $choice = "WHOIS";} 
                                                            else {echo 'https://godaddy.com/dpp/find?checkAvail=1&tmskey=&domainToCheck='.$domain.'.com'; $choice = "BUY";}
                                                        ?>"><?php echo $choice;?></a></td>
                            <td><?php if($twitter) echo "Not Available"; else echo "Available"; ?></td>
                        </tr>
                        <?php
                                }
                            }
                        ?>
            </tbody>
        </table>
</section>

<?php
    function domaincheck($domain) {                        
        $apiURL = 'https://api.ote-godaddy.com/v1/domains/available?domain='.$domain.'.com&checkType=FULL&forTransfer=false';
        $headers = array(
            'Accept: application/json',
            'Authorization: sso-key 2s83YZiNsc_4FA55fRhZZDerw3mMAs1vk:4FA7mRHWDfgCNxisgfTMsS'
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        curl_close($curl);
        $jresponse = json_decode($response, true);
        return $jresponse;
    }

    function depth_picker($arr, $temp_string, &$collect) {
        if ($temp_string != "") 
            $collect []= $temp_string;
    
        for ($i=0; $i<sizeof($arr);$i++) {
            $arrcopy = $arr;
            $elem = array_splice($arrcopy, $i, 1);
            if (sizeof($arrcopy) > 0) {
                depth_picker($arrcopy, $temp_string ." " . $elem[0], $collect);
            } else {
                $collect []= $temp_string. " " . $elem[0];
            }   
        }   
    }

    function twitterAccountExists($username){
        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => "https://twitter.com/".$username ) );
        $headers = explode( "\n", curl_exec( $curl ) );
        curl_close( $curl );
        
        if(strpos($headers[0], '404') !== false ) {
            return false;
        } else {
            return true;
        }
    }

?>