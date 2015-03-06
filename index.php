<html> 
<head>
<?php
  $queue=$_GET['queue'];
 echo "<meta http-equiv='refresh' 
content='5;url=index.php?queue=$queue'>";
?>
 <title>Queue Statistics</title>

  <style type="text/css">
  td.large {
  color:red;
  text-align:center;
  font-size:36pt;
  }
  </style>

  <style type="text/css">
  td.medium {
  color:red;
  text-align:center;
  font-size:18pt;
  }
  </style>

  <style type="text/css">
  tr.heading {
  color:blue;
  text-align:center;
  font-size:26pt;
  }
  </style>

  <style type="text/css">
  tr.heading-medium {
  color:blue;
  text-align:center;
  font-size:16pt;
  }
  </style>

</head>
 <body>
<center>

<?php
  $queue=$_GET['queue'];
  require_once('phpagi/phpagi-asmanager.php');

$myfile = '/etc/asterisk/queues_additional.conf';
echo "<form id='queue' action='index.php' method='GET'>";
$lines = file($myfile);    
 echo "Select the queue - ";
foreach($lines as $queues){
 if (preg_match("/]/i", $queues)) {
 echo "<button name='queue' type='submit' value='".substr($queues, 1, 
-2)."'>".substr($queues, 1, -2)."</button>";
 }
}
echo "</form>";
echo "<br />";

  $asm = new AGI_AsteriskManager();
  if($asm->connect())
  {
    $result = $asm->Command("queue show $queue");

    // ECHO THE QUEUE STATUS FIRST

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading';><td>Queue Number</td><td>Calls in 
queue</td><td>Answered calls</td><td>Abandoned calls</td><td>Average 
hold time</td><td>Average talk time</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/talktime/i", $line)) {
          echo "<tr>";
          $pieces = explode(" ", $line);
          echo "<td class='large';>$pieces[0] </td>";
          echo "<td class='large';>$pieces[2] </td> ";
          echo "<td class='large';>".trim($pieces[14], "C:,")."</td> ";
          echo "<td class='large';>".trim($pieces[15], "A:,")."</td> ";
          echo "<td class='large';>".trim($pieces[9], "(s")." s</td> ";
          echo "<td class='large';>".trim($pieces[11], "s")." s</td> ";
          echo "</tr>";
         }
      }
      echo "</table>";
    }



// Create two columns
echo "<br /><table border='0'>";
echo "<tr><td width=500px;>";


// Create two columns
echo "<br /><table border='0'>";
echo "<tr><td width=600px;>";

// ECHO AGENTS AVAILABLE

   echo "<h3><u>Agents logged in</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading-medium';><td>Agent ID</td><td>Status</td><td>Last call taken (seconds ago)</td><td>Calls today</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
        if (preg_match("/      .*has taken \d{0,2} calls|has taken no calls yet/i", $line)) {
          $pieces2 = preg_split('/\)\s\(|\s\(|\)\s/i',  trim(substr($line, 0, -1)));
          echo "<tr>";
          echo '<td class="medium">' . $pieces2[0] . '</td>';
          echo '<td class="medium">' . $pieces2[4] . '</td>';          
          echo '<td class="medium">' . preg_replace("/[^0-9]/","", $pieces2[6]) .' </td> ';
          echo '<td class="medium">' . preg_replace("/[^0-9]/","", $pieces2[5]) .' </td> ';
          echo "</tr>";
        }
      }
      echo "</table>";
    }

echo "</td><td>";

echo "</td></tr></table>";
    $asm->disconnect();
  }
?>
</center>
</body>
</html>
