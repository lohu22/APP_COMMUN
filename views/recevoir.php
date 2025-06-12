<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ====== 可配置部分 ======
$portName = 'COM8';
$baudRate = 9600;
$bits = 8;
$stopBit = 1;
$runDurationSeconds = 10;
$messageToSend = "Hello from PHP!\n";
// ========================

function echoFlush($msg) {
    echo $msg . "<br>\n";
    flush();
    ob_flush();
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>PHP Serial Test</title></head><body>";
echo "<h2>🔧 串口调试工具 (PHP + DIO)</h2>";
echoFlush("✅ 初始化串口 {$portName}，波特率 {$baudRate}...");

// 设置串口参数
exec("mode {$portName} baud={$baudRate} data={$bits} stop={$stopBit} parity=n xon=off", $output);
echoFlush("📄 串口配置结果：<br>" . implode("<br>", $output));

// 打开串口
$serialPort = @dio_open("\\\\.\\{$portName}", O_RDWR);
if (!$serialPort) {
    echoFlush("❌ 无法打开串口 {$portName}");
    exit;
}
echoFlush("✅ 串口打开成功！");

// 发送数据
$bytesSent = dio_write($serialPort, $messageToSend);
echoFlush("📤 已发送 {$bytesSent} 字节：<pre>{$messageToSend}</pre>");

// 监听接收
$endTime = time() + $runDurationSeconds;
echoFlush("⏳ 接收数据中（{$runDurationSeconds} 秒）...");

while (time() < $endTime) {
    $data = dio_read($serialPort, 256);
    if ($data) {
        echoFlush("📥 接收：<pre>" . htmlspecialchars($data) . "</pre>");
    }
    usleep(100000); // 每100ms检查一次
}

// 关闭串口
dio_close($serialPort);
echoFlush("✅ 串口关闭完成");

echo "</body></html>";
?>
