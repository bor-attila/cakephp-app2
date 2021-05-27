## On windows host install vagrant winnfsd plugin if composer needed
## vagrant plugin install vagrant-winnfsd

$init_script = <<-SCRIPT
echo "CREATE DATABASE vagrant" | mysql -uvagrant -pvagrant
echo "CREATE DATABASE test_vagrant" | mysql -uvagrant -pvagrant
SCRIPT

$start_script = <<-SCRIPT
mailhog > /dev/null & disown
sleep 5
SCRIPT

Vagrant.configure("2") do |config|
    config.vm.box = "atee/fatbox"
    config.vm.box_version = "1.1.0"
    config.vm.box_check_update = true
    config.vm.provider "virtualbox" do |v|
        v.linked_clone = true
        v.name = "myapp_cakephp_app"
        v.memory = 2048
        v.cpus = 2
    end
    config.vm.network "private_network", type: "dhcp"
    config.vm.network "forwarded_port", guest: 443, host: 2442
    config.vm.network "forwarded_port", guest: 8025, host: 8025
    config.vm.synced_folder ".", "/home/vagrant/www"
    config.vm.provision "shell", inline: $init_script
    config.vm.provision "shell", inline: $start_script, run: "always"
end
