<script>
  import { onMount } from "svelte";
  import { push } from "svelte-spa-router";

  let blodsukker = "";
  let note = "";
  let message = "";

  onMount(() => {
    if (localStorage.getItem("token") === null) {
      push("/")
    }
  })

  async function sendLog() {

    message = "";

    const token = localStorage.getItem("token");

    try {

      const res = await fetch(
        "https://www.flettedehvaler.dk/blodsukker/logger.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            token,
            blodsukker,
            note
          })
        }
      );

      const data = await res.json();

      if (data.success) {

        message = "Log gemt";

        blodsukker = "";
        note = "";

      } else {

        message = "Fejl ved logning";

      }

    } catch (e) {

      message = "Server fejl";

    }
  }
</script>

<style>
  :global(body) {
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    background: #f4f6f8;
  }

  .container {
    display: flex;
    justify-content: center;
    margin-top: 80px;
  }

  .box {
    background: white;
    padding: 30px;
    width: 300px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  }

  h1 {
    text-align: center;
    margin-bottom: 20px;
  }

  .field {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
  }

  label {
    margin-bottom: 5px;
    font-size: 14px;
  }

  input, textarea {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
  }

  button {
    width: 100%;
    padding: 10px;
    background: #1976d2;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  button:hover {
    background: #1565c0;
  }

  .msg {
    margin-top: 15px;
    text-align: center;
  }
</style>

<div class="container">
  <div class="box">

    <h1>Log</h1>

    <div class="field">
      <label for="blodsukker">Blodsukker</label>
      <input
        id="blodsukker"
        type="number"
        step="0.1"
        bind:value={blodsukker}
      >
    </div>

    <div class="field">
      <label for="note">Note</label>
      <textarea
        id="note"
        rows="3"
        bind:value={note}>
      </textarea>
    </div>

    <button on:click={sendLog}>Log</button>

    {#if message}
      <div class="msg">{message}</div>
    {/if}

  </div>
</div>