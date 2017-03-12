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
  	
	# Lib to fix windows line endings
    sudo apt-get install -y tofrodos

    # Create the .env file in the root web folder for database access locally
ENV_FILE=$(cat <<EOF
DB_NAME=scotchbox
DB_USER=root
DB_PASSWORD=root
DB_HOST=localhost
EOF
)
	echo "${ENV_FILE}" > /var/www/public/.env

    # Create the virtualhost
VHOST=$(cat <<EOF
	<VirtualHost *:80>
            ServerName ectv.local
            DocumentRoot /var/www/public/web
    </VirtualHost>
EOF
)

    echo "${VHOST}" > /etc/apache2/sites-available/scotchbox.local.conf

	# Jump into the web folder
	cd /var/www/public/

    # Run composer update
    composer update

    # Import data from fixture file
	fromdos bin/import-sql
    sudo chmod +x bin/import-sql

    # Import the data fixtures
    source bin/import-sql root root scotchbox fixtures/data.sql

    # restart apache
    service apache2 restart

    echo "Virtual machine successfully created"

  SHELL

end