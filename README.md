Kizzang Admin
=======
## Kizzang Admin

### For development environment

This project using `Vagrant` and `Chef Solo` for create development environment

##### Edit Vagrantfile

1. Make sure that you are using correct the  path to `kizzangChef` p4's depot

		CHEF_PATH = "/Development/kizzangChef"

2. The IP of Vagrant box

		config.vm.network "private_network", ip: "192.168.33.104"

##### Run Vagrant

		vagrant up		
		

##### Run the webapp

		http://192.168.33.104
