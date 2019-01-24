<?php
    /* 
    --------------------------WOOF--------------------------  
               __.                                              
        .-".'                      .--.            _..._    
      .' .'                     .'    \       .-""  __ ""-. 
     /  /                     .'       : --..:__.-""  ""-. \
    :  :                     /         ;.d$$    sbp_.-""-:_:
    ;  :                    : ._       :P .-.   ,"TP        
    :   \                    \  T--...-; : d$b  :d$b        
     \   `.                   \  `..'    ; $ $  ;$ $        
      `.   "-.                 ).        : T$P  :T$P        
        \..---^..             /           `-'    `._`._     
       .'        "-.       .-"                     T$$$b    
      /             "-._.-"               ._        '^' ;   
     :                                    \.`.         /    
     ;                                -.   \`."-._.-'-'     
    :                                 .'\   \ \ \ \         
    ;  ;                             /:  \   \ \ . ;        
   :   :                            ,  ;  `.  `.;  :        
   ;    \        ;                     ;    "-._:  ;        
  :      `.      :                     :         \/         
  ;       /"-.    ;                    :                    
 :       /    "-. :                  : ;                    
 :     .'        T-;                 ; ;        
 ;    :          ; ;                /  :        
 ;    ;          : :              .'    ;       
:    :            ;:         _..-"\     :       
:     \           : ;       /      \     ;      
;    . '.         '-;      /        ;    :      
;  \  ; :           :     :         :    '-.      
'.._L.:-'           :     ;          ;    . `. 
                     ;    :          :  \  ; :  
                     :    '-..       '.._L.:-'  
                      ;     , `.                
                      :   \  ; :                
                      '..__L.:-'
    */

    namespace TheRealKS\Watchdog;

    require('../vendor/autoload.php');

    $subscription = $_GET['sub'];
    $interval = $_GET['interval'];

    if (!is_int($interval)) {
        $interval = 3;
    }

    require('logistics/subscription.php');

    use TheRealKS\Watchdog\Logistics;

    $currentsubscription = new Logistics\Subscription($subscription); 

    use Igorw\EventSource\Stream;

    foreach(Stream::getHeaders() as $name => $value) {
      header("$name: $value");
    }

    $stream = new Stream();

    while (true) {
        $currentsubscription->poll();
        if ($currentsubscription->hasUpdate()) {
            $json = json_encode($currentsubscription->getUpdate());
            $id = "source_update_" . $currentsubscription->getUpdateNum();
            $stream
                ->event()
                    ->setId($id)
                    ->setData($json)
                ->end()
                ->flush();    
        }

        sleep($interval);
    }

?>