🚀 STHENOS
🏋️‍♂️ Sistema de Gestão de Treinos e Performance
Este projeto é um ecossistema simplificado para academias de bairro, focado em eliminar as fichas de papel e digitalizar a relação entre professor e aluno. O sistema permite a prescrição de treinos personalizados, controle de cargas e acompanhamento de frequência (check-in).

🛠️ Sobre este Repositório
Este projeto faz parte do meu plano de estudos em PHP. O objetivo principal é aplicar conceitos de:

Arquitetura MVC (Model-View-Controller).

Manipulação de Banco de Dados SQL (MySQL/MariaDB).

Autenticação e Níveis de Acesso (ACL).

Integração de Frontend com Lógica de Backend.

🤖 Gênese do Projeto (IA como Stakeholder)
Diferente de um projeto de curso comum, este sistema foi desenvolvido simulando uma relação real de Cliente vs. Desenvolvedor. Utilizei uma IA para atuar como o dono do negócio, definindo regras de negócio e restrições.

📝 O Prompt Inicial
"Sou desenvolvedor Web Júnior em PHP e gostaria de praticar minhas habilidades. Simule ser um cliente interessado em contratar um sistema. Restrição importante: evite temas que envolvam burocracias reais ou integrações oficiais complexas, como emissão de notas fiscais (MEI), gateways de pagamento reais ou qualquer regulamentação do Banco Central. Foque em um sistema com regras de negócio interessantes, mas que opere em um cenário 'fictício' ou simplificado."

📄 O Briefing Gerado (Resposta da IA)
"Fala, dev! Tudo certo? Curti a iniciativa. A melhor forma de sair do nível júnior é resolvendo problemas do mundo real, mesmo que o "cliente" seja fictício.

Vou entrar no personagem. Imagine que eu sou o dono de uma rede local de academias de bairro (a "FitFlow") e estou cansado de usar planilhas de Excel que sempre travam.

O Briefing: Sistema de Gestão de Treinos FitFlow
"Cara, é o seguinte: minhas unidades estão crescendo e eu não aguento mais os alunos perdendo a ficha de treino de papel ou os professores tendo que lembrar de cabeça o que cada um faz. Preciso de um sistema web simples, mas funcional, para organizar isso."

O que eu preciso (Requisitos):
Painel do Professor:

O professor precisa cadastrar alunos.

Ele deve conseguir criar "Fichas de Treino". Cada ficha tem um nome (ex: "Treino A - Hipertrofia") e uma lista de exercícios.

Cada exercício precisa ter: Nome, Séries, Repetições e Carga (kg).

Painel do Aluno:

O aluno faz login e só vê o treino dele.

Ele precisa conseguir marcar um "Check-in" no treino do dia (só pra eu saber que ele foi treinar).

Diferencial (O "Pulo do Gato"):

Quero um gráfico ou um contador simples no painel do professor que mostre quantos alunos treinaram hoje vs. o total de alunos cadastrados.

Por que isso é bom para você?
CRUD completo: Você vai lidar com Usuários, Exercícios e Treinos.

Relacionamentos SQL: Você terá que relacionar alunos com treinos e treinos com exercícios (Muitos-para-Muitos ou Um-para-Muitos).

Autenticação: Vai precisar de um sistema de login com diferentes níveis de acesso (Admin/Professor vs. Aluno).

Sem burocracia: Nada de boletos ou notas fiscais. O foco é puramente a lógica de gestão.

Como vamos seguir?
Para começar, eu não vou te dar o código pronto. Quero que você pense como o profissional que vai entregar isso.

O que você acha de começarmos desenhando como seria o seu Banco de Dados (as tabelas) para esse sistema? Se quiser, pode listar os campos aqui e eu te dou o feedback se o "cliente" aprovou a estrutura!"

📋 Funcionalidades Planejadas
👨‍🏫 Módulo do Professor
[ ] Cadastro e gestão de alunos.

[ ] Criação de fichas de treino (Exercícios, Séries, Repetições e Cargas).

[ ] Dashboard com métricas de assiduidade dos alunos.

🤳 Módulo do Aluno
[ ] Visualização do treino do dia via Mobile-Web.

[ ] Sistema de Check-in para registro de atividade.

[ ] Histórico de evolução de cargas.

🏗️ Estrutura de Dados (Preview)
Abaixo, a modelagem inicial das tabelas para suportar o sistema:

🚀 Como Rodar o Projeto
Clone o repositório:

Bash
git clone https://github.com/crocodiles128/Sthenos.git
Configure o seu ambiente local (XAMPP, Laragon ou Docker). (eu uso xampp)

Importe o arquivo database.sql (disponível na pasta /db).

Configure as credenciais no arquivo config.php.

📝 Licença
Este projeto é para fins de estudo. Sinta-se à vontade para explorar e sugerir melhorias!