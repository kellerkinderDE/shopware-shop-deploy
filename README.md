# shopware-shop-deploy
This repository contains a [Deployer](https://deployer.org/) configuration for Shopware 6.

## Usage
Copy the entire `.deployment` directory from the `example` directory of this repository into your project and modify it to suit your needs.

Use [`.gitlab-ci.yml`](./example/.gitlab-ci.yml) as a base, if you're using GitLab CI. Use [`.github/`](./example/.github) as a base for GitHub Actions.

### Assumptions
* We assume that JavaScript and CSS files will be built in CI and published to the deployment jobs as artifacts.
* We assume that all plugins are required via composer (custom plugins are placed in `custom/static-plugins`).

## Configuration
### `deploy.yaml`
#### Required adjustments
* Change the server hostname to you server
* `cachetool` => Needs to be the path of your php socket - if you want to use `cachetool:clear:opcache` in the deployment steps
* `deploy_path` => The path where the deployment should be setup on the server
* `remote_user` & `hostname` should be changed according to your data
* `application` => Change this value to your project name

#### Optional adjustments
* `branch` => Should be the target branch which is used for the deployment
* `port` => If your SSH port is not `22` you have to adjust this

### `deploy.php`
#### Required adjustments
* `plugins` => Should contain a list of plugins which are automatically installed and activated (eg. managed by deployment)
* `source_directory` => Needs to be the path of your project root based on `.deployment/`, usually this is already correct as it is

#### Optional adjustments
* `keep_releases` => Defines the number of previous releases which are kept on the server

### `deploy.php`
#### Optional adjustments
* `rsync` => You might want to adjust the ignored files according to your project setup and file structure

## Specific tasks of our deployment file
* `shopware6:plugins:install_update`
  * Handles the installation, update and activation of plugins defined in the `plugins` section of the `deploy.php`
* `shopware6:update`
  * Executes the `system:update:prepare` and `system:update:finish` command of Shopware
* `shopware6:messenger:stop`
  * Executes `messenger:stop-workers` to reset workers
    * This requires the workers to be started automatically, e.g. via `supervisord`
* `shopware6:bundle:dump`
  * Executes `bundle:dump` and is needed to publish the prebuilt `js` files
* `shopware6:theme:compile`
  * Will execute the `theme:compile` command on the server

### Additional notes
* `deployer` will create several directories in your deployment path on the server:
  * `.dep`: Information about the releases
  * `current`: Symlink that will always be linked to the latest release. Use it for your document root.
  * `releases`: This directory contains the last 5 releases. If you want to keep more or less releases, simply overwrite the `keep_releases` setting as stated above.
  * `shared/`: Here you can add additional files that will persist between deployments (like the already shared `.env` or `.htaccess`)

## Contribution
Feel free to send pull requests if you have any optimizations. They will be highly appreciated.

## License
MIT, see [`LICENSE`](./LICENSE)
