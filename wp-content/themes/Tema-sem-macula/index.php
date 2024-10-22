<?=get_header()?>



<style>
  .player-stats {
    font-family: "Noto Sans JP", sans-serif;
    display: flex;
    align-items: center;
    background-color: whitesmoke;
    margin-bottom: 10px;
    border-radius: 30px;
    padding-left: 20px;
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
<body>
  <a href="<?php echo home_url('createPlayer') ?>">cadastrar jogador</a>
  <?php 
      $url = "http://localhost:3000/player";

      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);

      curl_close($ch);

      $data = json_decode($response);

      if (json_last_error() !== JSON_ERROR_NONE) {
          echo "Erro na decodificação JSON: " . json_last_error_msg();
          exit;
      }
  ?>

  <h1>Selecione dois Jogadores para formar dupla</h1>
  <form action="">
    <select name="" id="">
      <?php 
        foreach($data->players as $player) {
          echo "<option value=" .$player->id .  ">" . $player->name . "</option>";
        }
      ?>
    </select>
    <select name="" id="">
      <?php 
        foreach($data->players as $player) {
          echo "<option value=" .$player->id .  ">" . $player->name . "</option>";
        }
      ?>
    </select>
    <button type="submit">Criar Dupla</button>
  </form>

  <?php 
      $url = "http://localhost:3000/single";

      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);

      curl_close($ch);

      $data = json_decode($response, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
          echo "Erro na decodificação JSON: " . json_last_error_msg();
          exit;
      }

      class Leaderboard {
        public $name;
        public $games_played;
        public $victories;
        public $loses;
        public $win_rate;

        public function __construct($name, $games_played, $victories, $loses, $win_rate) {
          $this->name = $name;
          $this->games_played = $games_played;
          $this->victories = $victories;
          $this->loses = $loses;
          $this->win_rate = $win_rate;
        }
      }

      foreach ($data['singleMatch'] as $match) {
        $playerOneId = $match['player_one_id'];
        $playerTwoId = $match['player_two_id'];
        $playerOneName = $match['player_one']['name'];
        $playerTwoName = $match['player_two']['name'];
        $result = $match['result'];
    
        // Inicializa os jogadores se não existirem
        if (!isset($playersStats[$playerOneId])) {
            $playersStats[$playerOneId] = [
                'name' => $playerOneName,
                'games_played' => 0,
                'victories' => 0,
                'loses' => 0
            ];
        }

        if (!isset($playersStats[$playerTwoId])) {
            $playersStats[$playerTwoId] = [
                'name' => $playerTwoName,
                'games_played' => 0,
                'victories' => 0,
                'loses' => 0
            ];
        }

        // Atualiza as estatísticas
        $playersStats[$playerOneId]['games_played']++;
        $playersStats[$playerTwoId]['games_played']++;

        if ($result === $playerOneId) {
            $playersStats[$playerOneId]['victories']++;
            $playersStats[$playerTwoId]['loses']++;
        } else {
            $playersStats[$playerTwoId]['victories']++;
            $playersStats[$playerOneId]['loses']++;
        }
    }

    // Criando os objetos Leaderboard
    $leaderboard = [];
    foreach ($playersStats as $stats) {
        $win_rate = $stats['games_played'] > 0 ? ($stats['victories'] / $stats['games_played']) * 100 : 0;
        $leaderboard[] = new Leaderboard($stats['name'], $stats['games_played'], $stats['victories'], $stats['loses'], $win_rate);
    }

    foreach ($leaderboard as $player) {
      echo <<< HTML
        <div class="player-stats">
          <p class="player-name"> $player->name  </p>
          <p class="victory"> $player->victories V </p>
          <div class="winrate-gradient" style=" background: linear-gradient(
            to right, 
            #20de6e,
            #20de6e $player->win_rate%,
            #de2020 $player->win_rate%,
            #de2020);"
          ></div>
          <p class="lose"> $player->loses D </p>
          <p> Jogos: $player->games_played </p>
          <p> Win-rate: $player->win_rate %</p>
        </div>
      HTML;
    }
          // Jogos: {$player->games_played}, Derrotas: {$player->loses}, Win Rate: {$player->win_rate}%\n </p>

  ?>

</body>
</html>
<!-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  async function getPlayers() {

  const players = await axios.get("http://localhost:3000/player");

  console.log(players);
  
  }
  getPlayers().then((s) => console.log(s))
</script> -->