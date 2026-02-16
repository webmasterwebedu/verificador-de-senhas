<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Senha - WebEdu</title>
    <style>
        /* Mantendo sua estrutura e adicionando flexibilidade */
        body { 
            font-family: sans-serif; 
            line-height: 1.6; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }

        /* Torna a imagem do logo adaptável */
        img { max-width: 100%; height: auto; }

        /* Ajuste dos inputs para telas menores */
        input[type="password"], input[type="text"] { 
            padding: 10px; 
            width: 100%; /* No mobile ocupa tudo */
            max-width: 250px; /* Mantém o tamanho original em telas grandes */
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box;
            font-size: 16px; /* Evita zoom automático no iOS */
        }

        input[type="submit"] { 
            padding: 10px 20px; 
            background-color: #FF6B00; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-weight: bold; 
            margin-top: 10px;
        }

        /* Ajuste para o formulário não quebrar o layout */
        #formVerificar div {
            display: inline-block;
            width: 100%;
            max-width: 250px;
        }

        @media (max-width: 500px) {
            input[type="submit"] { width: 100%; max-width: 250px; }
        }

        input[type="submit"]:disabled { background-color: #ccc; cursor: not-allowed; }
        .resultado { margin-top: 20px; padding: 15px; border-radius: 4px; font-weight: bold; word-wrap: break-word; }
    </style>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-1876874-45"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-1876874-45');
    </script>
    
    
    
</head>
<body>


    <img src="https://webedu.com.br/wp/wp-content/uploads/2012/11/logo_webedu_g3.png" alt="Logo WebEdu"/>
    
    <h1>Verificador de senhas vazadas</h1>
    <p>
        Verifique se sua senha já apareceu em vazamentos públicos. 
        Não salvamos a senha digitada, ela é usada apenas para este teste rápido.
    </p>

    <form method="post" id="formVerificar">
        <div style="position: relative; display: inline-block;">
            <input type="password" name="senha" id="senhaInput" placeholder="Digite a senha para teste..." required 
                   style="padding-right: 45px;">
            
            <span id="togglePassword" style="
                position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                cursor: pointer; color: #666; font-size: 18px; user-select: none;
                transition: color 0.2s;
            " 
            title="Mostrar senha">👁️</span>
        </div>
        <input type="submit" id="btnSubmit" value="Verificar Agora">
    </form>

    <div id="status">
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $senha_user = trim($_POST["senha"] ?? '');

        if ($senha_user === '') {
            echo "<div class='resultado' style='color: #cc0000; background: #ffe6e6;'>Informe uma senha para testar.</div>";
        } else {
            // 1) Calcula o SHA-1 da senha (maiúsculo), como o Pwned Passwords usa
            //    A senha NÃO é salva em banco nem em arquivo.
            $sha1 = strtoupper(sha1($senha_user)); // [web:115][web:121]
            $prefix = substr($sha1, 0, 5);
            $suffix = substr($sha1, 5);

            // 2) Consulta a API Pwned Passwords (modelo k-anonymity) [web:96][web:104][web:107][web:110]
            $url = 'https://api.pwnedpasswords.com/range/' . $prefix;

            // Recomenda-se definir um user-agent simples [web:110][web:120]
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: WebEdu-PwnedCheck/1.0\r\n"
                ]
            ];
            $context = stream_context_create($opts);
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                echo "<div class='resultado' style='color: #cc0000; background: #ffe6e6;'>
                        Não foi possível consultar o serviço de senhas vazadas no momento. 
                        Tente novamente mais tarde.
                      </div>";
            } else {
                // 3) A resposta vem com várias linhas: SUFIXO:COUNT [web:96][web:104][web:111]
                $linhas = explode("\n", $response);
                $encontrou = false;
                $vezes = 0;

                foreach ($linhas as $linha) {
                    $linha = trim($linha);
                    if ($linha === '') continue;

                    list($hash_sufixo, $count) = explode(':', $linha);
                    if (strtoupper($hash_sufixo) === $suffix) {
                        $encontrou = true;
                        $vezes = (int)$count;
                        break;
                    }
                }

                if ($encontrou) {
                    echo "<div class='resultado' style='color: #cc0000; background: #ffe6e6;'>
                            ⚠️ ATENÇÃO: Esta senha já apareceu em vazamentos públicos 
                            <strong>{$vezes}</strong> vez(es).
                            <br>Recomendação: troque esta senha imediatamente em todos os serviços onde a utiliza
                            e ative autenticação em duas etapas (2FA) sempre que possível.
                          </div>";
                } else {
                    echo "<div class='resultado' style='color: #006600; background: #e6ffed;'>
                            ✅ BOM SINAL: Esta senha não foi encontrada na base pública consultada.
                            <br>Ainda assim, use senhas longas, únicas para cada site e, se possível, um gerenciador de senhas.
                          </div>";
                }
            }
        }
    }
    ?>
    </div>

    <script>
        // Trava o botão para evitar cliques duplos
        document.getElementById('formVerificar').onsubmit = function() {
            var btn = document.getElementById('btnSubmit');
            btn.value = 'Consultando base de senhas vazadas...';
            btn.disabled = true;
        };
    </script>

    <div style="background: #e8f4f8; border: 1px solid #b3d9e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <h3 style="color: #0066cc; margin-top: 0;">🔒 Como funciona e por que é 100% SEGURO</h3>
        
        <p><strong>✅ NÓS NÃO GUARDAMOS SUA SENHA:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Sua senha é transformada em um "código único" (hash SHA-1) <em>imediatamente</em> no nosso servidor</li>
            <li>Esse código é <strong>impossível de reverter</strong> para descobrir a senha original</li>
            <li>A senha real <strong>nunca é salva</strong> em banco, log ou arquivo</li>
        </ul>

        <p><strong>✅ A API externa não vê sua senha:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Enviamos <strong>só os 5 primeiros caracteres</strong> do código para a API oficial do Have I Been Pwned</li>
            <li>A API devolve uma lista de 500 códigos similares (modelo k-anonymity)</li>
            <li><strong>Nós fazemos a comparação localmente</strong>, sem revelar sua senha</li>
        </ul>

        <p><strong>✅ Método usado por grandes empresas:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>1Password, Bitwarden, Firefox e Microsoft usam exatamente esta técnica</li>
            <li>Base com <strong>bilhões de senhas vazadas</strong>, sempre atualizada</li>
            <li><strong>Zero conhecimento</strong> da sua senha real por qualquer serviço</li>
        </ul>

        <p style="background: #fff3cd; padding: 12px; border-left: 4px solid #ffc107; margin: 15px 0;">
            <strong>💡 Use apenas para testar suas próprias senhas.</strong> 
            Se aparecer em vazamentos, troque imediatamente e ative 2FA!
        </p>
        
        <p><strong>
            Por que é seguro (explicação técnica)<br /><br />
            Senha do usuário exemplo: "123456"<br /><br />
            1. Seu servidor calcula: sha1("123456") = 7c4a8d09ca3762af61e59520943dc26494f8941b<br />
            2. Pega só os 5 primeiros: "7C4A8"<br />
            3. Chama API: https://api.pwnedpasswords.com/range/7C4A8<br /><br />
            4. API devolve lista tipo:<br /><br />
               7C4A8D09CA3762AF61E59520943DC26494F8941B:1000000<br />
               7C4A8D09CA3762AF61E59520943DC26494F8941C:5000<br />
               ... (outras 500 hashes)<br />
            5. O PHP procura "7C4A8D09CA3762AF61E59520943DC26494F8941B" na lista<br />
            6. Se acha ele mostra: "Esta senha vazou 1.000.000 vezes ! que nesse caso 123456 é."<br />
            A API nunca vê a senha nem o hash completo. Seu servidor nunca salva a senha.<br />
        </strong></p>
        
        <p style="font-size: 0.9em; color: #666; margin-top: 20px;">
            <strong>Fonte dos dados:</strong> API oficial Pwned Passwords (haveibeenpwned.com) • 
            <strong>Política de privacidade:</strong> Não armazenamos dados de senhas
        </p>
    </div>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7805341505350601"
     crossorigin="anonymous"></script>
<!-- webedu senhas -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-7805341505350601"
     data-ad-slot="2084065132"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const senhaInput = document.getElementById('senhaInput');
            const toggleIcon = this;
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleIcon.textContent = '🙈'; // Olho fechado
                toggleIcon.title = 'Ocultar senha';
                toggleIcon.style.color = '#ef4444';
            } else {
                senhaInput.type = 'password';
                toggleIcon.textContent = '👁️'; // Olho aberto
                toggleIcon.title = 'Mostrar senha';
                toggleIcon.style.color = '#666';
            }
        });
    </script>
</body>
</html>