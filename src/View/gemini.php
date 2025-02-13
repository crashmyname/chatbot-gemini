<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Gemini Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            margin: 10px auto;
            padding: 15px;
            background-color: #fff;
            max-width: 600px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        button[disabled] {
            /* Menargetkan button dengan atribut disabled */
            background-color: #ccc;
            /* Warna abu-abu untuk menandakan disabled */
            cursor: not-allowed;
            /* Mengubah kursor menjadi "not allowed" */
            opacity: 0.7;
            /* Membuatnya sedikit transparan */
        }

        #response-container,
        #error-container {
            display: none;
        }

        #response-text {
            white-space: pre-wrap;
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <h1>AI Gemini Chat</h1>

    <!-- Form Input Pertanyaan -->
    <form id="gemini-form" method="POST" action="<?= base_url() ?>/gemini">
        <?= csrf() ?>
        <label for="question">Tanyakan sesuatu:</label><br>
        <textarea name="question" id="question" rows="4" cols="50" placeholder="Ketik pertanyaan Anda..." required></textarea><br>
        <button type="submit" id="submit">Kirim</button>
        <button disabled style="display:none" id="loading"> Loading ..</button>
    </form>

    <!-- Kontainer untuk Menampilkan Jawaban -->
    <div id="response-container" class="card">
        <h2>Jawaban:</h2>
        <div id="response-text"></div>
    </div>

    <!-- Kontainer untuk Menampilkan Kesalahan -->
    <div id="error-container" class="card">
        <h2>Terjadi Kesalahan:</h2>
        <p id="error-message"></p>
    </div>

    <!-- Script AJAX + Typewriter Effect -->
    <script>
        document.getElementById('gemini-form').addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah reload halaman
            const submit = document.getElementById('submit');
            const loading = document.getElementById('loading');
            submit.style.display = "none";
            loading.style.display = "block";
            // Ambil data form
            const form = event.target;
            const formData = new FormData(form);

            // Kosongkan elemen respon/error sebelumnya
            const responseContainer = document.getElementById('response-container');
            const errorContainer = document.getElementById('error-container');
            const responseText = document.getElementById('response-text');
            const errorMessage = document.getElementById('error-message');

            responseContainer.style.display = 'none';
            errorContainer.style.display = 'none';
            responseText.innerText = '';
            errorMessage.innerText = '';

            try {
                // Kirim data menggunakan fetch
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Penanda AJAX request
                    },
                    body: formData
                });

                // Validasi respons dari server
                const result = await response.json();

                if (response.ok && result.response) {
                    submit.style.display = "block";
                    loading.style.display = "none";
                    responseContainer.style.display = 'block';

                    const fullResponse = Array.isArray(result.response) ? result.response.join(' ') : result.response;
                    const trimmedResponse = fullResponse.trim();

                    // Cek apakah hasil merupakan kode yang diawali dan diakhiri ```
                const codeMatch = trimmedResponse.match(/^```(?:\w+)?\n([\s\S]*?)\n```$/);

                if (codeMatch) {
                    // Jika hasil adalah kode, tampilkan dalam elemen <pre><code>
                    responseText.innerHTML =
                        `<pre><code>${codeMatch[1].replace(/</g, "&lt;").replace(/>/g, "&gt;")}</code></pre>`;
                }
                // Cek jika hasil adalah "** test **", ubah menjadi title
                else if (trimmedResponse === "** test **") {
                    responseText.innerHTML = `<h2>Test</h2>`;
                    }
                    // Efek mengetik untuk hasil biasa
                    else {
                        typeWriterEffect(fullResponse, responseText, 20);
                    }
                }

            } catch (error) {
                // Tampilkan error di layar
                console.error('Error:', error);
                errorMessage.innerText = error.message;
                errorContainer.style.display = 'block';
            }
        });

        // Fungsi Typewriter Effect
        function typeWriterEffect(text, element, speed) {
            let index = 0;
            const typing = setInterval(() => {
                if (index < text.length) {
                    element.innerText += text[index];
                    index++;
                } else {
                    clearInterval(typing); // Hentikan efek jika sudah selesai
                }
            }, speed);
        }
    </script>
</body>

</html>
