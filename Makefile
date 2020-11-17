example1:
	rm -Rf out;
	cd generator;
	./flow starter:kickstart examples/ex1.json out/


updateFromNeosDemo:
	# git subtree add --prefix generator/DistributionPackages/Neos.Starter/Features/Sites/NeosDemo/Neos.Demo https://github.com/neos/Neos.Demo.git master --squash
	git subtree pull --prefix generator/DistributionPackages/Neos.Starter/Features/Sites/NeosDemo/Neos.Demo https://github.com/neos/Neos.Demo.git master --squash
