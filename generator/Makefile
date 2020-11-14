example1:
	rm -Rf out;
	./flow starter:kickstart examples/ex1.json out/

updateFromNeosDemo:
	rm -Rf Neos.Demo
	git clone https://github.com/neos/Neos.Demo.git
	cp Neos.Demo/{.editorconfig,.eslintignore,.eslintrc,.gitignore,.jshintrc,.nvmrc,.prettierignore,.prettierrc,.stylelintrc,.yarnclean,babel.config.js,package.json,postcss.config.js,webpack.config.js,webpack.packages.js} DistributionPackages/Neos.Starter/Classes/Features/Frontend/WebpackBuildSetup/
