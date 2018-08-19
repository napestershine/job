set :stages,        %w(live dev)
set :default_stage, "dev"
set :stage_dir,     "app/config/deploy"
require 'capistrano/ext/multistage'
