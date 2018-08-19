set :application, "Kantipur Job Production [kantipurjob.com]"
set :domain, "202.51.74.221"
set :deploy_to, "/var/www/html/kantipurjob"
set :app_path, "app"

set :user, "ubuntu"

set :group, "www-data"
set :use_sudo, false
ssh_options[:forward_agent] = true
default_run_options[:pty] = true


set :repository, "git@gitlab.com:yarshastudio/jobs-portal.git"
set :scm, :git
set :deploy_via,  :remote_cache
set :branch, "master"
set :interactive_mode,  false

set :symfony_console, "bin/console"
set :composer_bin, "composer"
set :use_composer, true
set :update_vendors, true
set :copy_vendors, false

set :shared_files, ["app/config/parameters.yml"]
set :shared_children, [app_path + "/logs", web_path + "/uploads", app_path + "/sessions", web_path + "/media"]
set :writable_dirs, ["app/cache", "app/logs", "app/sessions"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true

set  :keep_releases, 2

set :model_manager, "doctrine"

role :web, domain
role :app, domain, :primary => true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

# Run migrations before warming the cache
before "symfony:cache:warmup", "symfony:doctrine:schema:update"

after "deploy:setup", "upload_parameters"
after "deploy:update", "deploy:cleanup"

namespace :customtasks do
    task :cleanup, :except => {:no_release => true} do
      count = fetch(:keep_releases, 3).to_i
      run "ls -1dt #{releases_path}/* | tail -n +#{count + 1} | sudo xargs rm -rf"
    end
end

task :upload_parameters do
  origin_file = "app/config/parameters_prod.yml"
  destination_file = shared_path + "/app/config/parameters.yml"

  try_sudo "mkdir -p #{File.dirname(destination_file)}"
  top.upload(origin_file, destination_file)
end

# Run Job After Deployment
namespace :symfony do
  desc "Run Yarn Install"
  task :yarn_install, :roles => :app, :only => { :primary => true} do
    capifony_pretty_print "--> Yarn Install Started"

    run "#{try_sudo} sh -c 'cd #{latest_release} && yarn'"
    capifony_puts_ok
  end
end


# production Log
namespace :symfony do
  desc "Get Production Log"
    task :prod_log, :roles => :app, :only => { :primary => true} do
        capifony_pretty_print "--> Production logs"

        run "#{try_sudo} sh -c 'tail -f #{latest_release}/var/logs/prod.log'"
        capifony_puts_ok
    end
end
