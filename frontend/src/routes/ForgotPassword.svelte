<script>
  let email = "";
  let loading = false;
  let message = "";
  let success = false;

  async function sendReset() {
    message = "";
    success = false;

    if (!email) {
      message = "Indtast din email";
      return;
    }

    loading = true;

    try {
      const formData = new FormData();
      formData.append("email", email);

      const response = await fetch("https://flettedehvaler.dk/blodsukker/forgot_password.php", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      success = !!data.success;
      message = data.message || "Hvis emailen findes, er der sendt et reset-link.";
    } catch (error) {
      success = false;
      message = "Der opstod en fejl. Prøv igen.";
    } finally {
      loading = false;
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
    color: #333;
  }

  input {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  input:focus {
    outline: none;
    border-color: #1976d2;
  }

  button {
    width: 100%;
    padding: 10px;
    background: #1976d2;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    margin-top: 10px;
  }

  button:hover {
    background: #1565c0;
  }

  button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .message {
    font-size: 13px;
    margin-top: 10px;
    padding: 10px;
    border-radius: 4px;
    background: #f1f1f1;
    color: #333;
  }

  .message.success {
    background: #e8f5e9;
    color: #1b5e20;
  }

  .message.error {
    background: #ffebee;
    color: #b71c1c;
  }
</style>

<div class="container">
  <div class="box">
    <h1>Glemt password</h1>

    <div class="field">
      <label for="email">Email</label>
      <input
        id="email"
        type="email"
        bind:value={email}
        placeholder="Indtast din email"
      >
    </div>

    <button on:click={sendReset} disabled={loading}>
      {#if loading}
        Sender...
      {:else}
        Send
      {/if}
    </button>

    {#if message}
      <div class:success={success} class:error={!success} class="message">
        {message}
      </div>
    {/if}
  </div>
</div>