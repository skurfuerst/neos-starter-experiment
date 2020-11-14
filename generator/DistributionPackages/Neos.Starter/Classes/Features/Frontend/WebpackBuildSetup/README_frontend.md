## About the frontend build stack

We included a frontend build stack based on
[Node.js](https://nodejs.org/),
[NVM](https://github.com/nvm-sh/nvm#readme),
[webpack](https://webpack.js.org/) and [Yarn](https://yarnpkg.com/). The
[webpack](https://webpack.js.org/) configuration includes
[Babel](https://babeljs.io/), [PostCSS](https://postcss.org/) and
[Sass](https://sass-lang.com/).

> **Note:** If you want to have a build stack for a Mono-Repo, you can
> take a look at our [Neos.io](https://github.com/neos/Neos.NeosIo)
> package.

### Installation

Make sure that [Node.js](https://nodejs.org/) and
[Yarn](https://yarnpkg.com/) are installed. It is recommended to use
[NVM](https://github.com/nvm-sh/nvm#readme) to manage versions of the
[Node.js](https://nodejs.org/) versions.

``` {.bash}
# Enable the correct nvm
nvm use
# Install the package dependencies
yarn
```

### Commands

  Command           Description
  ----------------- ---------------------------------------------------
  `yarn build`      Builds all assets
  `yarn pipeline`   Runs install and then build all assets
  `yarn start`      Watches the sources and rebuilds assets on change

### Package management

The dependencies are stored in the [package.json](package.json) file, so
if you edit any config, who need new packages (Like
[React](https://reactjs.org/), [Vue.js](https://vuejs.org/),
[TypeScript](https://www.typescriptlang.org/), etc.), you have to add
them to this file. You can read more about this
[here](https://nodejs.dev/the-package-json-guide).

### [webpack.packages.js](webpack.packages.js)

In this file, we set the files we want to render. Currently, we render
only one Main.js and Main.css files, but if you\'re going to have
multiple files for your package, here is the point where you can add
them.

### Explanation of the config files:

  File Name                                Explantion
  ---------------------------------------- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  [.editorconfig](.editorconfig)           [EditorConfig](https://editorconfig.org/) helps maintain consistent coding styles
  [.eslintignore](.eslintignore)           These files get ignored from [ESLint](https://eslint.org/)
  [.eslintrc](.eslintrc)                   The configuration file for [ESLint](https://eslint.org/), a pluggable JavaScript linter
  [.jshintrc](.jshintrc)                   The configuration for [JSHint](https://jshint.com/), a static code analysis tool for JavaScript
  [.nvmrc](.nvmrc)                         This file contains the required [Node.js](https://nodejs.org/) version and is used by [NVM](https://github.com/nvm-sh/nvm#readme)
  [.prettierignore](.prettierignore)       These files gets excluded from the [Prettier](https://prettier.io/) code formatting
  [.prettierrc](.prettierrc)               This is the configuration file for [Prettier](https://prettier.io/)
  [.stylelintrc](.stylelintrc)             This is the configuration file for [StyleLint](https://stylelint.io/)
  [.yarnclean](.yarnclean)                 Cleans and removes unnecessary files from package dependencies
  [babel.config.js](babel.config.js)       This is the configuration file for [Babel](https://babeljs.io/). If you want to enable something like [React](https://reactjs.org/) [TypeScript](https://www.typescriptlang.org/) or [Vue.js](https://vuejs.org/), here is the right place to do this
  [package.json](package.json)             In this file all your dependencies from the build stack are stored
  [webpack.config.js](webpack.config.js)   This is the configuration for [webpack](https://webpack.js.org/)
  [yarn.lock](yarn.lock)                   This is the lockfile for [Yarn](https://yarnpkg.com/). This is needed to get consistent installs across machines
