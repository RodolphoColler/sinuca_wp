<?php get_header() ?>
<body>
  <h1>Cadastro de jogador</h1>
  <?php 
    $url = "http://localhost:3000/player";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response);
  ?>
  <form action="" id="form-player">
    <label for="">
      Procure um jogador:
      <input type="text" id="search-player">
    </label>
    <button type="submit" id="create-player-button">Cadastrar Jogador</button>
  </form>
  <div class="players-container">
    <?php 
      foreach($data->players as $player) {
        echo <<< HTML
          <p class="players-name">$player->name</p>
        HTML;
      }
    ?>
  </div>
  <script>
    const players = document.querySelectorAll(".players-name");
    const input = document.getElementById("search-player");
    const playerContainer = document.querySelector(".players-container");

    input.addEventListener("keyup", ({ target }) => {

      if(!target.value) {
        playerContainer.innerHTML = "";
        players.forEach(element => { playerContainer.appendChild(element); });
        return;
      }

      const filteredPlayers = [...players].filter((element) => {
        if(element.textContent.toLowerCase().includes(target.value.toLowerCase())) {
          return element
        }
      })

      playerContainer.innerHTML = "";

      filteredPlayers.forEach((element) => {
        const p = document.createElement('p');
        p.innerHTML = element.textContent;
        playerContainer.appendChild(p);
      });

    })
    

  </script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    const form = document.getElementById('form-player');
    async function createPlayer(name) {
      const player = await axios.post("http://localhost:3000/player", { name: name });
    }

    const playersName = [...players].map(element => element.textContent);

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      const { value } = document.getElementById("search-player");

      const isPlayerExistent = playersName.every((element) => element.toLowerCase() !== value.toLowerCase());

      if (!isPlayerExistent) {
        alert("jogador já existente");
        return;
      }

      createPlayer(value);
      alert("jogador criado, atualize a página");
    })
  </script>
</body>
</html>