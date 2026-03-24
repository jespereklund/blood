<script>
  import { push } from "svelte-spa-router";

  let email = "";
  let password = "";
  let passwordRepeat = "";

  let error = "";

  async function createUser() {

    error = "";

    if (password !== passwordRepeat) {
      error = "Passwords matcher ikke.";
      return;
    }

    try {

      const res = await fetch(
        "https://www.flettedehvaler.dk/blodsukker/signup.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            email,
            password
          })
        }
      );

      const data = await res.json();

      if (!data.success) {
        error = data.error;
        return;
      }

      push("/");

    } catch (e) {

      error = "Server fejl. Prøv igen senere.";

    }
  }
</script>

<style>
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
    font-size: 14px;
    margin-bottom: 5px;
  }

  input {
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

  .error {
    margin-top: 15px;
    color: red;
    font-size: 14px;
    text-align: center;
  }
</style>

<div class="container">
  <div class="box">

    <h1>Opret bruger</h1>

    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" bind:value={email}>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input id="password" type="password" bind:value={password}>
    </div>

    <div class="field">
      <label for="passwordRepeat">Gentag password</label>
      <input id="passwordRepeat" type="password" bind:value={passwordRepeat}>
    </div>

    <button on:click={createUser}>Opret</button>

    {#if error}
      <div class="error">{error}</div>
    {/if}

  </div>
</div>