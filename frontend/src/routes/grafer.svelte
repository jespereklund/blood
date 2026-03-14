<script>
import { onMount } from "svelte";
import { push } from "svelte-spa-router";
import Chart from "chart.js/auto";

let chart;
let error = "";
let logs = []; // gem data til tabel

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

        logs = data.logs; // gem logs

        const labels = logs.map(v => v.created_at);
        const values = logs.map(v => v.blodsukker);

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

.error {
    color: red;
}

table {
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ccc;
    padding: 8px;
}

.chart-container {
    width: 800px;
    height: 600px;
}
</style>

<div>

<h1>Målinger</h1>

{#if error}
<p class="error">{error}</p>
{/if}

<div class="chart-container">
    <canvas id="chart"></canvas>
</div>

{#if logs.length > 0}
<h2>Data</h2>

<table>
<thead>
<tr>
<th>Dato</th>
<th>Blodsukker</th>
</tr>
</thead>

<tbody>
{#each logs as log}
<tr>
<td>{log.created_at}</td>
<td>{log.blodsukker}</td>
</tr>
{/each}
</tbody>
</table>
{/if}

</div>