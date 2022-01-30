<?php
    // Extended Euclidean Algorithm
    function EEA($a, $b, $t1, $t2)
    {
        // if ($a < $b) {
        //     $temp = $a;
        //     $a = $b;
        //     $b = $temp;
        // }

        $rest = $a % $b;
        $q = (int)($a / $b);
        $t3 = $t1 - $t2 * $q;
        $a = $b;
        $b = $rest;
        $t1 = $t2;
        $t2 = $t3;
        if ($b == 0) {
            return $t1;
        } else return EEA($a, $b, $t1, $t2);
    }

    //The Chinese Remainder Theorem 
    function CRT_v1($a, $m)
    {
        $len_m = count($m);
        $M = 1;
        $Mi = [];
        $M_1 = [];
        $R = [];
        $r = 0; 
        for ($i=0; $i <  $len_m; $i++)
            $M *= $m[$i];
            
        for ($i=0; $i < $len_m; $i++)
        {
            array_push($Mi, $M / $m[$i]);
            array_push($M_1, EEA($m[$i], $Mi[$i], 0, 1) < 0 ? EEA($m[$i], $Mi[$i], 0, 1) + $m[$i] : EEA($m[$i], $Mi[$i], 0, 1));
            array_push($R,$a[$i] * $Mi[$i] * $M_1[$i]);
            $r += $R[$i];
            $r = $r % $M;
        }
        if( $r < 0 ) $r += $M; 
        return [$M, $Mi, $M_1, $R, $r];
    }

    function CRT_v2($a, $m)
    {
        $len_m = count($m);
        $r = 0;
        $M = 1;
        for ($i=0; $i <  $len_m; $i++)
            $M *= $m[$i];

        for ($i=0; $i < $len_m; $i++)
            $r += $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1)) ;
        
        $r = $r % $M;
        if( $r < 0 ) $r += $M;
        
        return $r; 
    }

    function CRT_v3($a, $m)
    {
        $len_m = count($m);
        $R = [0, 0, 0, 0];
        $M = 1;
        for ($i=0; $i <  $len_m; $i++)
            $M *= $m[$i];

        for ($i=0; $i < $len_m; $i++){
            $R[0] += $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1) ) ;
            $R[0] %= $M; 
            $R[1] -= $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1)) ;
            $R[1] %= $M; 
            if ($i%2 === 0)
            {
                $R[2] -= $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1)) ;
                $R[2] %= $M; 
                $R[3] += $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1)) ;
                $R[3] %= $M; 
            }else
            {
                $R[2] += $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i]) ;
                $R[2] %= $M; 
                $R[3] -= $a[$i] * ($M / $m[$i]) * (EEA($m[$i], ($M / $m[$i]), 0, 1) < 0 ? EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i] : EEA($m[$i], ($M / $m[$i]), 0, 1) + $m[$i]) ; 
                $R[3] %= $M; 
            }             
        }
        for ($i=0; $i < 4; $i++) { 
        //     $R[$i] %= $M;
        if ($R[$i] < 0 ) $R[$i] += $M;
        }
        return $R;
    }

    function is_prime($number): bool
    {
        if ($number  == 2) return true;
        if (($number % 2) == 0) return false;
        $sqrt = (int)(sqrt($number));
        for ($i = 3; $i <= $sqrt; $i += 2) {
            if (($number % $i) == 0) return false;
        }
        return true;
    }

    function is3mod4($n)
    {
        if($n % 4 == 3)
            return true;
        return false;
    }

    function key_generation($p, $q)
    {
        if((!is_prime($p) || !is_prime($q))||(!is3mod4($p) || !is3mod4($q)))
        {
            print("modifier q et p");
            return;
        }

        return $p * $q;
    }

    function rabin_encryption($m , $n)
    {
        return gmp_mod(gmp_pow($m, 2), $n);
    }

    function rabin_decryption_v1($c, $p, $q)
    {
        return [CRT_v2([(int) gmp_mod(gmp_pow($c, (1 + $p) / 4), $p), (int) gmp_mod(gmp_pow($c, (1 + $q) / 4),  $q)],[$p, $q]),
        CRT_v2([(int) -gmp_mod(gmp_pow($c, (1 + $p) / 4), $p), (int) -gmp_mod(gmp_pow($c, (1 + $q) / 4), $q)],[$p, $q]),
        CRT_v2([ (int) -gmp_mod(gmp_pow($c, (1 + $p) / 4), $p) , (int) gmp_mod(gmp_pow($c, (1 + $q) / 4), $q)],[$p, $q]),
        CRT_v2([ (int) gmp_mod(gmp_pow($c, (1 + $p) / 4), $p), (int) -gmp_mod(gmp_pow($c, (1 + $q) / 4), $q)],[$p, $q])];
    }

    function rabin_decryption_v2($c, $p, $q)
    {
        $a = (int) gmp_mod(gmp_pow($c, (1 + $p) / 4), $p);
        $b = (int) gmp_mod(gmp_pow($c, (1 + $q) / 4), $q);
        return CRT_v3([$a, $b],[$p, $q]);
    }




/// testing of CRT_v1
    print(json_encode((CRT_v1([-1, -9], [7, 11])))."\n");

/// testing of CRT_v2
    print(CRT_v2([-1, -9], [7, 11])."\n");

/// testing of CRT_v3
    print(json_encode((CRT_v3([1, 9], [7, 11])))."\n");

//testing of key_generation
    print(key_generation(7, 11)."\n");

/// testing of rabin_encryption
    print(rabin_encryption(20 , key_generation(7, 11))."\n");

/// testing of rabin_decryption_v1
    print(json_encode(rabin_decryption_v1(15, 7, 11))."\n");

/// testing of rabin_decryption_v2
    print(json_encode(rabin_decryption_v2(15, 7, 11))."\n");



   