Como funciona e por que é 100% SEGURO
✅ NÓS NÃO GUARDAMOS SUA SENHA:

Sua senha é transformada em um "código único" (hash SHA-1) imediatamente no nosso servidor
Esse código é impossível de reverter para descobrir a senha original
A senha real nunca é salva em banco, log ou arquivo
✅ A API externa não vê sua senha:

Enviamos só os 5 primeiros caracteres do código para a API oficial do Have I Been Pwned
A API devolve uma lista de 500 códigos similares (modelo k-anonymity)
Nós fazemos a comparação localmente, sem revelar sua senha
✅ Método usado por grandes empresas:

1Password, Bitwarden, Firefox e Microsoft usam exatamente esta técnica
Base com bilhões de senhas vazadas, sempre atualizada
Zero conhecimento da sua senha real por qualquer serviço
💡 Use apenas para testar suas próprias senhas. Se aparecer em vazamentos, troque imediatamente e ative 2FA!

Por que é seguro (explicação técnica)

Senha do usuário exemplo: "123456"

1. Seu servidor calcula: sha1("123456") = 7c4a8d09ca3762af61e59520943dc26494f8941b
2. Pega só os 5 primeiros: "7C4A8"
3. Chama API: https://api.pwnedpasswords.com/range/7C4A8

4. API devolve lista tipo:

7C4A8D09CA3762AF61E59520943DC26494F8941B:1000000
7C4A8D09CA3762AF61E59520943DC26494F8941C:5000
... (outras 500 hashes)
5. O PHP procura "7C4A8D09CA3762AF61E59520943DC26494F8941B" na lista
6. Se acha ele mostra: "Esta senha vazou 1.000.000 vezes ! que nesse caso 123456 é."


A API nunca vê a senha nem o hash completo. Seu servidor nunca salva a senha.

Fonte dos dados: API oficial Pwned Passwords (haveibeenpwned.com) • Política de privacidade: Não armazenamos dados de senhas.