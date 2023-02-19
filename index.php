<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWT Pure PHP</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>

<body class="m-0 p-0 border-box p-5">
    <div class="alert alert-info mb-5" role="alert">
        <strong class="expire-in">Token expire in 60 seconds</strong>
    </div>

    <div class="mb-5 d-flex align-center justify-content-between">
        <div class="w-100 p-2 border rounded-lg mr-3" id="created-token" name="created-token">
        </div>
        <div class="d-flex align-center">
            <button class="btn copy btn-outline-secondary mr-3" type="button">
                Copy
            </button>
            <button type="button" class="btn btn-primary" id="generate-token">
                Generate
            </button>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="secret-key">Secret Key</label>
            <input type="text" class="form-control" id="secret-key" placeholder="Secret Key" value="my-secret-key">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-8">
            <label for="validate-token">Validate Token</label>
            <input type="text" class="form-control" id="validate-token" placeholder="Validate Token">
        </div>
        <div class="form-group col-md-4">
            <label for="secret-key">Test Secret Key</label>
            <input type="text" class="form-control" id="test-secret-key" placeholder="Secret Key" value="my-secret-key">
        </div>
    </div>

    <div id="token-status"></div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script src="plugins/clipboard.min.js"></script>
    <script>
        new ClipboardJS('.btn');
    </script>

    <script>
        let interval;
        const createdToken = document.querySelector('#created-token');
        const tokenStatus = document.querySelector('#token-status');

        handleInitializeGenerateTokenBtn();
        handleInitializeCopyBtn();
        handleTokenOrTestSecretKeyChange();
        handleGenerateNewToken();

        async function handleGenerateNewToken() {
            const secret = document.querySelector('#secret-key').value;

            const response = await fetch('getToken.php', {
                method: 'POST',
                headers: {
                    'accept': '*',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    secret
                })
            });

            const {
                token
            } = await response.json();
            createdToken.innerText = token;
            handleCountdownTokenExpiration();
            await handleTokenValidation();
        }

        async function handleTokenOrTestSecretKeyChange() {
            const validateToken = document.querySelector('#validate-token');
            const secret = document.querySelector('#test-secret-key');

            const tokenOrTestSecretKeyChangeEventFunc = async () => {
                await handleTokenValidation();
            }

            removeEventListener('change', tokenOrTestSecretKeyChangeEventFunc);

            validateToken.addEventListener('change', tokenOrTestSecretKeyChangeEventFunc);
            secret.addEventListener('change', tokenOrTestSecretKeyChangeEventFunc);
        }

        async function handleTokenValidation() {
            const validateToken = document.querySelector('#validate-token').value;
            const secret = document.querySelector('#test-secret-key').value;

            const response = await fetch('validToken.php', {
                method: 'POST',
                headers: {
                    'accept': '*',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: validateToken,
                    secret
                })
            });

            const {
                status,
                message
            } = await response.json();

            tokenStatus.innerHTML = `
            <div class="alert alert-${status === 1 ? 'success' : 'danger'} mb-5" role="alert">
                <strong>${message}</strong>
            </div>
            `;
        }

        function handleInitializeGenerateTokenBtn() {
            const btnGenerateToken = document.querySelector('#generate-token');

            const btnGenerateTokenEventFunc = async () => {
                await handleGenerateNewToken();
            }

            removeEventListener('click', btnGenerateTokenEventFunc);
            btnGenerateToken.addEventListener('click', btnGenerateTokenEventFunc);
        }

        function handleInitializeCopyBtn() {
            const btnCopyJWT = document.querySelector('.btn.copy');

            const btnCopyEventFunc = () => {
                const createdJwt = document.querySelector('#created-token');
                btnCopyJWT.setAttribute('data-clipboard-text', createdJwt.innerText);
            };

            removeEventListener('click', btnCopyEventFunc);
            btnCopyJWT.addEventListener('click', btnCopyEventFunc);
        }

        function handleCountdownTokenExpiration() {
            let time = 59;
            const infoTokenExpireIn = document.querySelector('.expire-in');
            infoTokenExpireIn.innerText = `Token expire in 60 seconds`;
            if (interval) clearInterval(interval);
            interval = setInterval(async () => {
                if (time <= 0) clearInterval(interval);
                infoTokenExpireIn.innerText = `Token expire in ${time} seconds`;
                --time;
            }, 1000);
        }
    </script>
</body>

</html>