<script>
	import { onMount } from "svelte";

	let email = "";
	let token = "";
	let password = "";
	let password_confirm = "";

	let loading = false;
	let message = "";
	let success = false;

	onMount(() => {
		const params = new URLSearchParams(window.location.search);
		email = params.get("email") || "";
		token = params.get("token") || "";

        console.log(email, token)

		if (!email || !token) {
			message = "Ugyldigt reset-link";
		}
	});

	async function resetPassword() {
		message = "";
		success = false;

		if (!password || !password_confirm) {
			message = "Udfyld alle felter";
			return;
		}

		if (password !== password_confirm) {
			message = "Passwords matcher ikke";
			return;
		}

        /*
		if (password.length < 8) {
			message = "Password skal være mindst 8 tegn";
			return;
		}
        */

		loading = true;

		try {
			const formData = new FormData();
			formData.append("email", email);
			formData.append("token", token);
			formData.append("password", password);
			formData.append("password_confirm", password_confirm);

			const response = await fetch(
				"https://flettedehvaler.dk/blodsukker/reset_password.php",
				{
					method: "POST",
					body: formData
				}
			);

			const data = await response.json();

			success = !!data.success;
			if (data.message) {
				//console.log("url", window.location.origin)
			}
			message = data.message || "Noget gik galt";
		} catch (err) {
			message = "Serverfejl. Prøv igen.";
		} finally {
			loading = false;
		}
	}
</script>

<div class="container">
	<div class="box">
		<h1>Nulstil password</h1>

		{#if message}
			<div class="message" class:success={success} class:error={!success}>
				{message}
			</div>
		{/if}

		{#if !success}
			<div class="field">
				<label for="password">Nyt password</label>
				<input
					id="password"
					type="password"
					bind:value={password}
					placeholder="Nyt password"
				>
			</div>

			<div class="field">
				<label for="password_confirm">Gentag password</label>
				<input
					id="password_confirm"
					type="password"
					bind:value={password_confirm}
					placeholder="Gentag password"
				>
			</div>

			<button on:click={resetPassword} disabled={loading}>
				{#if loading}
					Gemmer...
				{:else}
					Gem nyt password
				{/if}
			</button>
		{/if}
	</div>
</div>

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