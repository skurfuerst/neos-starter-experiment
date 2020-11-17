example1:
	cd generator && ./flow starter:kickstart ../profiles/2020-09-a/fullProfile.json ../profiles/2020-09-a-full-instance


updateFromNeosDemo:
	# git subtree add --prefix generator/subtrees/Neos.Demo https://github.com/neos/Neos.Demo.git 6.2.0 --squash
	git subtree pull --prefix generator/subtrees/Neos.Demo https://github.com/neos/Neos.Demo.git 6.2.0 --squash
