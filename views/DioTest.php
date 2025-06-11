<?php


// Real time display on the web page
function echoFlush($string)
{
	echo $string . "\n";
	flush();
	ob_flush();
}



echoFlush( "<h3>Demo Serial Communication...</h3>" );



// Open the serial port

$portName = 'COM6';
$baudRate = 9600;
$bits = 8;
$stopBit = 1;

// ... for Windows

$serialPort = dio_open("\\\\.\\{$portName}", O_RDWR );

$output = array();
exec("mode {$portName} baud={$baudRate} data={$bits} stop={$stopBit} parity=n xon=on", $output);
$com_status = print_r($output, true);
echoFlush( str_replace("\n", "<br>", $com_status . "<br>") );
/*
// ... for Unix

$serialPort = dio_open($portName, O_RDWR | O_NOCTTY | O_NONBLOCK );

dio_fcntl($serialPort, F_SETFL, O_SYNC);

dio_tcsetattr($serialPort, array(
    'baud' => $baudRate,
    'bits' => $bits,
    'stop'  => $stopBit,
    'parity' => 0
));
*/
	
if(!$serialPort)
{
    echoFlush( "Could not open Serial port {$portName} ");
    exit;
}


// send data
$bytesSent = dio_write($serialPort, "\n\nHello from PHP !!!\n\n\n" );
echoFlush( "Sent: {$bytesSent} bytes...<br>" );


	
// read data during 10 seconds
$runForSeconds = new DateInterval("PT10S"); //10 seconds
$endTime = (new DateTime())->add($runForSeconds);

echoFlush( "Waiting for {$runForSeconds->format('%S')} seconds to recieve data on serial port...<br>" );

while (new DateTime() < $endTime) {

    $data = dio_read($serialPort, 256); //this is a blocking call
    if ($data) {
        echoFlush( str_replace("\n", "<br>", "[" . $data . "]") );
    }
}



// Close the serial port
dio_close($serialPort);

?>