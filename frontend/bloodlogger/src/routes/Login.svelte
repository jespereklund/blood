<script>
  import { push } from "svelte-spa-router";
  import Router, { link } from "svelte-spa-router"

  let email = "";
  let password = "";
  let error = "";

  async function login() {

    error = "";

    try {

      const res = await fetch(
        "https://www.flettedehvaler.dk/blodsukker/login.php",
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

      if (data.success === true) {

        push("/logger");

      } else {

        error = "adgang nægtet";

      }

    } catch (e) {

      error = "adgang nægtet";

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
    text-align: center;
    font-size: 14px;
  }

.links {
  margin-top: 25px;
  display: flex;
  width: 100%;
  gap: 10px;
}

.links a {
  flex: 1;
  width: 100%;
  text-align: center;
  padding: 8px 0;
  color: #1976d2;
  text-decoration: none;
  border-radius: 4px;
}

.links a:hover {
  background: #eef4ff;
}
  
</style>

<div class="container">
  <div class="box">

    <h1>Login</h1>

    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" bind:value={email}>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input id="password" type="password" bind:value={password}>
    </div>

    <button on:click={login}>Log ind</button>

    <div class="links">
      <a href="#/signup" use:link>Opret bruger</a>
      <a href="#/glemt-password" use:link>Glemt password?</a>
    </div>

    {#if error}
      <div class="error">{error}</div>
    {/if}

  </div>
</div>