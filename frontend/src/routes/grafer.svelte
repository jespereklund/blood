<script>
import { onMount } from "svelte";
import { push } from "svelte-spa-router";
import Chart from "chart.js/auto";

let chart;
let error = "";

async function loadGraph() {

    const token = localStorage.getItem("token");

    if (!token) {
        push("/");
        return;
    }

    try {

        const res = await fetch(
            "https://www.flettedehvaler.dk/blodsukker/grafer.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    token
                })
            }
        );

        const data = await res.json();

        if (!data.success) {
            error = "Adgang nægtet";
            return;
        }

        const labels = data.logs.map(v => v.created_at);
        const values = data.logs.map(v => v.value);

        const ctx = document.getElementById("chart");

        chart = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: "Blodsukker",
                        data: values,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true
            }
        });

    } catch (e) {

        error = "Kunne ikke hente data";

    }

}

onMount(loadGraph);
</script>

<style>

.container {
    max-width: 500px;
    margin: auto;
    padding: 40px;
    text-align: center;
}

canvas {
    margin-top: 30px;
}

.error {
    color: red;
}

</style>

<div class="container">

<h1>Grafer</h1>

{#if error}
<p class="error">{error}</p>
{/if}

<canvas id="chart"></canvas>

</div>