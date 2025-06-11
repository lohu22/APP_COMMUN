<?php
// === 配置串口 ===
$port = "COM6"; // Windows 示例；Linux/Mac 用 "/dev/ttyUSB0" 或 "/dev/cu.usbserial-XXXX"
$baudRate = 9600;

// === 打开串口 ===
$fd = dio_open($port, O_RDWR | O_NOCTTY | O_NONBLOCK);
if (!$fd) {
    die("无法打开串口 $port\n");
}

// === 配置串口参数 ===
dio_fcntl($fd, F_SETFL, 0);
dio_tcsetattr($fd, [
    'baud' => $baudRate,
    'bits' => 8,
    'stop' => 1,
    'parity' => 0
]);

// === 读取数据（等待串口传来数据） ===
sleep(2); // 等待Arduino准备好数据
$data = '';
$startTime = time();

while ((time() - $startTime) < 5) { // 最多等待5秒
    $buffer = dio_read($fd, 256); // 读取最多256字节
    if (!empty($buffer)) {
        $data .= $buffer;
        break;
    }
    usleep(100000); // 等待100ms
}

// === 关闭串口 ===
dio_close($fd);

// === 解析数据 ===
if (preg_match('/HUM:(\d+(\.\d+)?);TEMP:(\d+(\.\d+)?)/', $data, $matches)) {
    $humidity = $matches[1];
    $temperature = $matches[3];
    echo "Humidité : {$humidity}%RH<br>";
    echo "Temperature : {$temperature}℃<br>";
} else {
    echo "未能读取到有效数据：<pre>" . htmlspecialchars($data) . "</pre>";
}
?>
