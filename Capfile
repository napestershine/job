load 'deploy' if respond_to?(:namespace) # cap2 differentiators

require 'capifony_symfony2'
require 'capistrano-slack-notify'
load 'app/config/deploy/main'

set :slack_webhook_url, "https://hooks.slack.com/services/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"
before 'deploy', 'slack:starting'
after  'deploy', 'slack:finished'
before 'deploy:rollback', 'slack:failed'
after 'deploy:rollback', 'slack:rolled_back'