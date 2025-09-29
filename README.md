# Desafio Full Stack – 3Pontos Tech

Bem-vindo(a) ao processo seletivo da 3Pontos Tech. Este desafio avalia o seu domínio das tecnologias listadas, sua
capacidade de criar um código "limpo" e de comunicar decisões de forma clara.

## Projeto proposto

Este projeto é um clone simplificado do Reddit, com foco em funcionalidades essenciais de comunidades, postagens,
comentários e votos.

A aplicação permite que usuários explorem subreddits (comunidades temáticas), publiquem conteúdo em Markdown, interajam
por meio de comentários e realizem votos positivos ou negativos nos posts.

O objetivo é replicar, de forma objetiva e enxuta, a estrutura básica de um fórum de discussão como o Reddit, servindo
como base para estudos, testes técnicos ou evolução futura.

| Item          | Descrição                  |
|---------------|----------------------------|
| **Linguagem** | PHP 8.3^                   |
| **Framework** | Laravel 12 + FilamentPHP 4 |
| **Database**  | SQLite ou Postgres         |
| **Front-end** | Blade e Tailwind v4        |

> [!NOTE]  
> Use **SQLite** para agilizar a configuração. Caso deseje, inclua instruções para trocar facilmente para **PostgreSQL**
> ou **MySQL** com **Docker**.

## Critérios gerais de avaliação

1. **Modelagem de dados** – consistência, normalização e previsibilidade.
2. **Laravel / FilamentPHP** – uso adequado de recursos nativos (Eloquent, policies, actions, widgets, etc.).
3. **Design do Sistema** – Como deixar o sistema robusto.
4. **TailwindCSS** – domínio dos utilitários essenciais para layout e tipografia.
5. **Qualidade de código** – legibilidade, organização em camadas, nomenclatura coerente.
6. **Commits** – adoção de _Conventional Commits_ e histórico incremental.
7. **Documentação** – arquivo `DEVELOPMENT.md` escrito por você (sem geração automática por IA) contendo:
    - Visão geral da solução.
    - Justificativa das principais decisões técnicas.
    - Anotações do processo
8. **Capacidade analítica** – explicação dos trade-offs escolhidos.
9. **Painel Administrativo** - uso do FilamentPHP para gerenciar subreddits e posts.


> [!WARNING]
> Não nos importamos com o uso de I.A desde que você saiba justificar suas decisões. O principal é ter certeza da entrega feita e isso será questionado durante a avaliação.


> [!WARNING]
> Não será permitido uso de Plugins externos fora o `MediaLibrary` para agilizar a implementação de uma feature.

## Níveis e critérios de aceite

No projeto disponível no [Figma](https://bit.ly/3pontos-fs-challenge-figma), você encontrará **versões da interface**, cada uma representando um **nível de
complexidade** e refinamento visual correspondente a diferentes níveis de senioridade.

> Senha do figma: 'pontopontoponto'

Com base nessas telas, você deverá analisar a UI e modelar o sistema seguindo os critérios definidos abaixo:

| Nível            | Foco principal                                                                                                                           |
|------------------|------------------------------------------------------------------------------------------------------------------------------------------|
| **Básico**       | Conseguir entregar as funcionalidades centrais usando recursos básicos de Laravel, Filament e Tailwind.                                  |
| **Intermedário** | Ênfase em modelagem de dados sólida, separação clara de camadas, e testes de unidade básicos.                                            |
| **Avançado**     | Arquitetura escalável, uso avançado de Filament (Resources, Panels, Actions), políticas de segurança, testes abrangentes, CI/CD simples. |

## Desafio

Estamos iniciando um novo projeto: um **fórum de perguntas e respostas inspirado no Reddit** baseado em
subreddits/tópicos.

> A criação desses subreddits deverá ser dinâmica usando o FilamentPHP.

Todo o desenvolvimento será guiado pelo layout disponível no Figma, que apresenta três páginas principais, repetidas em
diferentes estágios de evolução visual:

- **Home/Dashboard**: exibe todas as postagens para usuários logados ou não logados.
- **Subreddit**: página inicial de uma comunidade, listando todos os seus posts.
- **Post**: visualização de um post específico pertencente a uma comunidade/produto.

> Serão dois ambientes: admin (Filament) e front-end (Blade + Tailwind).

Você deverá utilizar o layout como base para modelar os dados do projeto, utilizando sua análise visual e interpretação.

> Você também poderá utilizar uma I.A para modelagem, porém caso use, deixe explicito no `DEVELOPMENT.md` quais foram suas decisões de aceite e o que foi feito por você.

Cada etapa implementada deve ser versionada com commits no seguinte formato:

```
git commit -m "feat(panel): sua-mensagem-de-preferencia-em-ingles"
git commit -m "feat(component): sua-mensagem-de-preferencia-em-ingles"
git commit -m "feat(anything): sua-mensagem-de-preferencia-em-ingles"
```

> Caso algum ponto do projeto não esteja claro, registre suas suposições no final do `README.md`.


---

## Etapas do Processo

### 1. Submissão do Projeto

1. Crie um novo repositório utilizando este template (no topo da pagina, no botão verde "Use this template");
2. Crie uma branch `develop` e faça todo o desenvolvimento nela;
3. Abra um Pull Request para a branch `main` do **seu repositório pessoal**.
4. Envie um e-mail para `daniel@3pontos.com` com uma breve apresentação sobre você e o link do Pull Request.
5. Aguarde nossa resposta.

> Data de entrega: **01/10/2025** às **13:00h**
> Você pode acompanhar outras submissões e discussões sobre no [Discord da 3Pontos](https://bit.ly/3pontos-discord).

### 2. Entrevista Técnica

Caso avance para a próxima fase, você participará de uma conversa com o time técnico. Vamos discutir suas decisões,
entender seu raciocínio e trocar ideias de forma descontraída e técnica ao mesmo tempo.

> Data das entrevistas: 02/10 ~ 03/10 na parte da tarde.

### 3. Conversa com a Liderança

Neste momento, você terá um bate-papo com as lideranças da empresa para avaliarmos o alinhamento cultural. Por mais que
pareça uma etapa “clichê”, ela é fundamental: buscamos pessoas que compartilhem dos nossos valores e estejam prontas
para crescer com o time.

### 4. Carta Proposta

Se tudo estiver alinhado, você receberá nossa proposta para integrar o time oficialmente e colar com a gente!

## Materiais de Suporte

- [Filament Brasil](https://filament.com.br)
- [Filament Docs](https://filament.com/docs)
- [https://refactoring.guru/](https://refactoring.guru/)
- [http://br.phptherightway.com/](http://br.phptherightway.com/)
- [https://www.php-fig.org/psr/psr-12/](https://www.php-fig.org/psr/psr-12/)

## Agradecimentos

- [@jeffersongoncalves](https://github.com/jeffersongoncalves) pelo setup inicial do Filament V4 Beta.
- [@nexturhe4rt](https://github.com/nexturhe4rt) pelo UI feita.
- [@gvieira18 - vulgo usb777](https://github.com/gvieira18) pelo setup inicial do projeto.