# -*- mode: ruby -*-
# vi: set ft=ruby :

# Epic CSV Table Viewr Vagrant instance by James Beattie
# ---------------------------------------------------------
#
# You will need to install Virtualbox and Vagrant to take advantage of this system.
#
# Check the project out from git and from the root of the project, run the following command line command
# > vagrant up
# then add a hosts entry for ectv with ip address 192.168.33.20 and you can access the local instance at http://ectv.local

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Up the memory
  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
  end

  config.vm.box = "scotch/box"
  config.vm.network "private_network", ip: "192.168.33.20"
  config.vm.hostname = "ectv"
  config.vm.synced_folder ".", "/var/www/public", :mount_options => ["dmode=777", "fmode=666"]
  config.ssh.password = 'vagrant'

  # Define the bootstrap file: A (shell) script that runs after first setup of your box (= provisioning)
  config.vm.provision :shell, inline: <<-SHELL

    // Create the .env file in the root web folder for database access locally
	# setup env file
ENV_FILE=$(cat <<EOF
DB_NAME=scotchbox
DB_USER=root
DB_PASSWORD=root
DB_HOST=localhost
EOF
)
	echo "${ENV_FILE}" > /var/www/public/.env

	# Jump into the web folder
	cd /var/www/public/

    # Run composer update
    composer update

    # Import data from fixture file
    sudo chmod +x bin/*

    # Import the data fixtures
    bin/import-sql root root scotchbox fixtures/data.sql

    echo "Virtual machine successfully created"

  SHELL

end