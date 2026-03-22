<script>
  import Router, { link } from "svelte-spa-router"
  import { push } from "svelte-spa-router";

  import Login from "./routes/Login.svelte"
  import Signup from "./routes/Signup.svelte"
  import ForgotPassword from "./routes/ForgotPassword.svelte"
  import Logger from "./routes/Logger.svelte"
  import Maalinger from "./routes/Maalinger.svelte"
  import Log from "./routes/Log.svelte"
  import ResetPassword from "./routes/ResetPassword.svelte";
  import { loggedInState } from "./LoggedIn.svelte";
  import { onMount } from "svelte";

  onMount(() => {
    loggedInState.loggenIn = (localStorage.getItem("token") === null) ? false : true
  })

  const routes = {
    "/": Login,
    "/signup": Signup,
    "/forgot-password": ForgotPassword,
    "/logger": Logger,
    "/maalinger": Maalinger,
    "/log": Log,
    "/reset-password": ResetPassword
  }

  function logout(e) {
    localStorage.removeItem("token")
    loggedInState.loggenIn = false
    push("/");
    e.preventDefault()
  }
</script>

<style>
  .topbar {
    background: #1976d2;
    color: white;
    display: flex;
    align-items: center;
    padding: 12px 20px;
    gap: 20px;
    font-family: sans-serif;
  }

  .title {
    font-weight: bold;
    margin-right: auto;
    font-size: 18px;
  }

  .navlink {
    color: white;
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 6px;
    transition: background 0.2s;
  }

  .navlink:hover {
    background: rgba(255,255,255,0.2);
  }

  .content {
    padding: 20px;
  }
</style>

<div class="topbar">
  <div class="title">Blodsukker Logger System</div>
  <a class="navlink" href="#/logger" use:link>Logger</a>
  <a class="navlink" href="#/maalinger" use:link>Målinger</a>
  {#if loggedInState.loggenIn}
    <a class="navlink" href="" onclick={logout}>Logout</a>
  {:else}
    <a class="navlink" href="#/" use:link>Login</a>  
  {/if}
</div>

<div class="content">
  <Router {routes} />
</div>