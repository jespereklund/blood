<script>
  import Router, { link } from "svelte-spa-router"
  import { push } from "svelte-spa-router";

  import Login from "./routes/Login.svelte"
  import Signup from "./routes/Signup.svelte"
  import ForgotPassword from "./routes/ForgotPassword.svelte"
  import Logger from "./routes/Logger.svelte"
  import Grafer from "./routes/grafer.svelte"
  import Log from "./routes/Log.svelte"


  const routes = {
    "/": Login,
    "/signup": Signup,
    "/forgot-password": ForgotPassword,
    "/logger": Logger,
    "/grafer": Grafer,
    "/log": Log
  }

  function logout() {
    localStorage.removeItem("token")
    push("/");
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
  <a class="navlink" href="#/" use:link>Login</a>
  <a class="navlink" href="#/logger" use:link>Logger</a>
  <a class="navlink" href="#/grafer" use:link>Målinger</a>
  <a class="navlink" href="" onclick={logout}>Logout</a>
</div>

<div class="content">
  <Router {routes} />
</div>