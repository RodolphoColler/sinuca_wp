<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">

  <style>
    .player-stats {
      font-family: "Noto Sans JP", sans-serif;
      display: flex;
      align-items: center;
      background-color: whitesmoke;
      margin-bottom: 10px;
      border-radius: 30px;
      padding-left: 20px;
      /* margin-top: 50px; */
      /* width: min-content; */
    }

    .player-name {
      width: 120px;
    }

    .victory {
      color: #20de6e;
      margin: 0 10px;
    }

    .lose {
      color: #de2020;
      margin: 0 10px;
    }

    .winrate-gradient {
      width: 150px;
      height: 4px;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <?php

  $url = "http://localhost:3000/duoMatch";

  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);

  curl_close($ch);

  $data = json_decode($response, true);


  if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erro na decodificação JSON: " . json_last_error_msg();
    exit;
  }

  class Duos {
    public $name1;
    public $name2;
    public $games_played;
    public $victories;
    public $loses;
    public $win_rate;

    public function __construct($name1, $name2, $games_played, $victories, $loses, $win_rate)
    {
      $this->name1 = $name1;
      $this->name2 = $name2;
      $this->games_played = $games_played;
      $this->victories = $victories;
      $this->loses = $loses;
      $this->win_rate = $win_rate;
    }
  }

  foreach ($data['duoMatch'] as $duoMatch) {

    $playerOneName = $duoMatch['duo_player']['player_one']['name'];
    $playerTwoName = $duoMatch['duo_player']['player_two']['name'];
    $duoOneId = $duoMatch['duo_one_id'];

    $playerThreeName = $duoMatch['duo_player_two']['player_one']['name'];
    $playerFourName = $duoMatch['duo_player_two']['player_two']['name'];
    $duoTwoId = $duoMatch['duo_two_id'];

    $result = $duoMatch['result'];
 

    // Inicializa os duos se não existirem
    if (!isset($duoStats[$duoOneId])) {
      $duoStats[$duoOneId] = [
        'name1' => $playerOneName,
        'name2' => $playerTwoName,
        'games_played' => 0,
        'victories' => 0,
        'loses' => 0
      ];
    }
    if (!isset($duoStats[$duoTwoId])) {
      $duoStats[$duoTwoId] = [
        'name1' => $playerThreeName,
        'name2' => $playerFourName,
        'games_played' => 0,
        'victories' => 0,
        'loses' => 0
      ];
    }

    // Atualiza as estatísticas
    $duoStats[$duoOneId]['games_played']++;
    $duoStats[$duoTwoId]['games_played']++;

    if ($result === $duoOneId) {
      $duoStats[$duoOneId]['victories']++;
      $duoStats[$duoTwoId]['loses']++;
    } else {
      $duoStats[$duoTwoId]['victories']++;
      $duoStats[$duoOneId]['loses']++;
    }
  }

  $Duos = [];

  foreach ($duoStats as $stats) {
    //calcular winrate
    $win_rate = $stats['games_played'] > 0 ? ($stats['victories'] / $stats['games_played']) * 100 : 0;

    //objeto Duos
    $Duos[] = new Duos($stats['name1'], $stats['name2'], $stats['games_played'], $stats['victories'], $stats['loses'], $win_rate);
  }


  foreach ($Duos as $duo) {
    echo <<< HTML
      <div class="player-stats">
        <p class="player-name"> $duo->name1 </p>
        <p class="player-name"> $duo->name2 </p>
        <p class="victory"> $duo->victories V </p>
        <div class="winrate-gradient" style=" background: linear-gradient(
          to right, 
          #20de6e,
          #20de6e $duo->win_rate%,
          #de2020 $duo->win_rate%,
          #de2020);"
        ></div>
        <p class="lose"> $duo->loses D </p>
        <p> Jogos: $duo->games_played |</p>
        <p> | Win-rate: $duo->win_rate %</p>
      </div>
    HTML;
  }

  ?>


</body>

</html>