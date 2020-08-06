# Slarty Bartfast V1.0

Slarty Bartfast is an artifact manager to help simplify build and deploy processes dealing with "artifacts" and "assets".

## What is an Artifact?

An artifact is the result of a code change - meaning it could be a compiled executable, a zip file containing transpiled javascript or even just the contents of a project directory if that's what is needed to allow the application to run on a server. In short, an artifact ultimate ends of being a zip file containing code or binaries or a combination of those which can be used whenever we'd otherwise need to build an application.

## What is an asset?

An asset is something your application needs in order to run, but it is not the result of your own code. It could be a third party library or other code or files that ideally don't change all that often. An example might be a version of the ExtJS framework. The application needs it to function, but it doesn't work with a normal package manager like npm or yarn. Slart Bartfast allows these to be stored in the repository as a tar.gz file and then retrieved and deployed via the `slarty deploy-assets` command.

## Why was Slarty Bartfast created?

At work, all of our projects use a "build" step for UI applications. These are typically React applications, but there's no reason this shouldn't work for any other application that takes a bunch of source code and creates a bundle or compiled artifact.  Each of our applications consists of multiple separate UI applications. In some cases some applications may not be modified or updated for a long time, and any given PR (pull request) likely only updates a single application. It doesn't make sense ot rebuild applications that haven't changed. This also gives a nice way to ensure that the exact same build of an application is what we use in QA, UAT, Production and any other environment.

Integrating Slarty into our PR and build process means that we are saving a ton of time by not doing work we don't need to. Deployments that previously built on the server can now download and deploy artifacts in a few seconds as well.

## What does Slarty Bartfast do?

Slarty Bartyfast uses git to uniquely identify the contents of directories. It currently will not work on non-git controlled code, but if that's important it may be a change in the future. The idea is that a project may contain several different applications or artifacts. Instead of building every sub-application every time there is a change or a pull request, if we can identify the contents that become or define a unique artifact, we can build it once and store it. Later on we can just download and use that artifact rather than recreating it.

By condensing the contents of a directory or directories down to a single unique value before building and using a predictable way to identify the resulting artifact file, we can quickly decide if a build is needed or if we've already built an artifact for this particular unique combination of code. Slarty Bartfast can, with the help of an `artifacts.json` configuration file, programmatically determine if a build is needed and kick it off if it is. Once it is done, it can store that artifact in a repository that it can later use to determine that it won't need to build again until the code is different.

Additionally, Slarty Bartfast can download and deploy those artifacts. Currently deployment is literally downloading and unzipping to the configured location. This download and unzipping process is very fast and saves a ton of time over building on the web server. It also means we don't need npm or node on the web server.

To summarize, Slart Bartfast allows for repeatable, predictable deploys and eliminates the need to rebuild artifacts that have been built already. 

# Configuration

Slarty Bartfast uses a json configuration file called `artifacts.json` by default. This file provides information that Slarty Bartfast uses to do its work. At the root, is an object with several keys. I'll talk about each of these sections and go into more detail where needed.

* **application** - The name of the application. This is not currently used
* **root_directory** - The location of the root directory of the project. Everything Slarty Bartfast does will be relative to that directory. For convenience, you can use the "__DIR__" value to indicate that the root of the project is the same as the location of the artifacts.json file. Changing the root directory and the application's locations relative to that will result in a different identifier value and could result in different archive contents even if the actual source hasn't changed. It's highly recommended to put artifacts.json in the project's root directory and use `__DIR__`
* **repository** - This is the configuration for where build artifacts should be stored. It will be discussed in detail below.
* **artifacts** - This is where you configure each of the builds. More on this later as well.
* **assets** - This is where you configure assets for deployment. More on this later too.

### Configuration - "repository" section

The "repository" section is where you configure the location where you'd like to store the results of building an artifact. It's where Slarty Bartfast will make the determination of if a build needs to be created, where to put the artifact and upon deployment, and where to pull artifacts for deployment.

Slarty Bartfast currently supports the local file system and Amazon's S3 as repository locations. The repository object requires an "adapter" key with either "local" or "s3" as the value. The value is case-insensitive. It also has an "options" key which is another object that defines the values we need in order to use the repository location.

#### Local Repository

The local repository configuration is simplest. The only value needed in options is "root". Here's a sample local configuration:

```
{
  ...
  "repository": {
    "adapter": "Local",
    "options": {
      "root": "/tmp/artifact-repo"
    }
  },
  ...
}
```

#### S3 Repository

To use AWS S3 as a repository a few more options are required. Here's an example:

```
{
   ...
   "repository": {
    "adapter": "s3",
    "options": {
      "region": "us-east-1",
      "bucket-name": "<aws bucket name>",
      "path-prefix": "path/to/repo",
      "profile": "default (optional)"
    }
  },
  ...
}
``` 

Most of the values should be obvious what they are for. The path-prefix is the only optional value. If provided, it will result in the artifacts being placed in pseudo-directories on S3. It can be a good way to keep different applications' artifacts in the same bucket but keep them separated. You should have your AWS credentials in the `~/.aws/credentials` file of the user that will be running Slarty Bartfast. The profile key is optional and will default to "default", but if you would like Slarty to use credentials from a different profile section in your credentials file, this is where to put that.

### Configuration - "artifacts" section

The artifacts section is an array of objects. Each of those objects defines the information needed to determine how to calculate the identifier, how to name the artifact, how to cause a build to happen and where to unzip an artifact to deploy.

An example:

```
{
      "name": "Models",
      "directories": ["src/SlartyBartfast/Model"],
      "command": "make Model",
      "output_directory": "src/SlartyBartfast/Model",
      "deploy_location": "build/murdles",
      "artifact_prefix": "slarty-models",
      "root": "optional/override"
    }
```

* **name** - The name of the artifact or build is used in the output of various Slarty Bartfast commands
* **directories** - Though the name is "directories" it will also work with individual files. These are used to determine the unique identifier. The idea is if anything in one or more of the directories has changed then the build output would be different. If files outside of these paths change and it causes different output from the build process, then those files or directories should be included in this array.
* **command** - This is the command that is executed to create the build output. It should be executable from the application's root directory
* **output_directory** - This is the directory that will be zipped to form the archive file that will be stored in the repository
* **deploy_location** - This is the location where the archive should be unzipped to
* **artifact_prefix** - This value is used in part of the naming of the archive zip file. The archive name is essentially {archive_prefix}-{hash}.zip. It helps identify what the artifact belong to or came from if looking on the file system.
* **root** - The root value at the artifact level is optional and you may never need to use it. By default, each artifact will use the root directory from the root of the configuration. If you need, for some reason, to calculate a hash from a different starting location for an application, you could provide that different root here. Again, in most cases you will not need this.

## Configuration - "assets" section

The assets section is an array of objects. Each object defines the information needed to retrieve an asset from the artifact repository as well as where it should be unzipped to. At this time, the assets section only is used for the `deploy-assets` command. No other command uses or is aware of this section. 

An example:

```
{
  "name": "ExtJS 4.2",
  "filename": "extjs-4.2.tar.gz",
  "deploy_location": "library/extjs-4.2"
}
```

* **name** - The name of the artifact is a friendly name that can be used to filter. You can limit the deploy-assets command to only deploying some assets by filtering on this name.

* **filename** - This is the name of the file that should be found in the artifact repository. At this time the file must exist in the same location as all the other artifacts.

* **deploy_location** - This is the location where the asset will be downloaded and expanded. After expansion the asset archive itself will be removed.

## Slarty Bartfast Commands

Slarty Bartfast provides a number of commands. All are executed with ./slarty or /path/to/slarty.

### ./slarty hash <root\> <directories...\>

The hash command does not require artifacts config. The root value is where to start calculating the hash from and the directories are space separated relative paths to use when calculating the hash. The order of the provided directories will not affect the hash result.

```
➜  SlartyBartfast git:(master) ✗ ./slarty hash ~/Projects/myproject mydirectory

 c39bffc99a4277c31ad8185a8e2a0919bbe44a82
```

If the directories do not exist or are empty, you'll see an error. Please note: MacOSX does not use a case sensitive file system by default but git is case-sensitive. Please ensure the directories and configuration match the actual case of the files or directories.

### ./slarty artifact-names

The `artifact-names` command can accept [-c|--config] and [-f|--filter] options. Both are optional. If no configuration file is specified, it will default to ./artifacts.json. The filter option is used to provide a list of applications for which to provide artifact names. The result of this command is a table of applications paired with the artifact name for the current state of the repo:

```
➜  SlartyBartfast git:(master) ✗ ./slarty artifact-names
 ------------- --------------------------------------------------------------
  Application   Artifact Name
 ------------- --------------------------------------------------------------
  source        slarty-source-15ab98133cfacf640b76d7fdf7890211110e5041.zip
  Services      slarty-services-91f042b9df7c50b59ab08c657d09c81442e04a65.zip
  Models        slarty-models-51286ac4976b8dc1667d8f7bc033806e858cb7b7.zip
  AMess         slarty-mess-f2788bbe13c3240951a10d97593467a68502e2f7.zip
 ------------- --------------------------------------------------------------

```

### ./slarty hash-application

Similarly to artifact-names, hash-application takes the same [-c|--config] and [-f|--filter] options. Instead of an artifact name, it provides the hashes alone.

```
➜  SlartyBartfast git:(master) ✗ ./slarty hash-application
 ------------- ------------------------------------------
  Application   Hash
 ------------- ------------------------------------------
  source        15ab98133cfacf640b76d7fdf7890211110e5041
  Services      91f042b9df7c50b59ab08c657d09c81442e04a65
  Models        51286ac4976b8dc1667d8f7bc033806e858cb7b7
  AMess         f2788bbe13c3240951a10d97593467a68502e2f7
 ------------- ------------------------------------------
```

### ./slarty should-build

The `should-build` command accepts the same options as most of the Slarty Bartfast commands - [-c|--config] and [-f|--filter]. The purpose of this command is to determine if the artifact archive exists in the repository. If it does exist then a build is not needed. If it does not, then a build would be needed. This command does that determination but without actually doing the builds.

```
➜  SlartyBartfast git:(master) ✗ ./slarty should-build
 ------------- --------------
  Application   Build Needed
 ------------- --------------
  source        YES
  Services      YES
  Models        NO
  AMess         NO
 ------------- --------------
```

In the example above, the artifacts for the `Models` and `AMess` applications already exist in the repository, but the builds for the `source` and `Services` applications do not exist in the repository.

### ./slarty do-builds

The `do-builds` command, like most above also accepts the `[-c|--config]` and `[-f|--filter]`. It also accepts a `--force` option. Running `do-builds` will determine the name of the artifact that should result from a build. If it exists in the repo, then it will not be executed. If it does not exist, then the `command` part of the artifacts configuration will be executed. Once the build succeeds, the archive will be created by zipping the `output_directory` into an archive named like what you'd see in the `artifact-names` command. It then stores that archive in the repository.

If you provide the `--force` option, then it will not check if the archive exists in the repository. It will build and store the result in the repository which means if it did exist, it will be overwritten. If the build process changed but the code did not, this would be a good way to ensure that the proper artifact archive is what is stored in the repo.

```
Doing build for source - YES
Doing build for Services - YES
Doing build for Models - NO
Doing build for AMess - NO

Beginning build for source application
--------------------------------------

<snip ...> 

 Build succeeded for source
 1/2 [==============>-------------]  50%
-- Saved slarty-source-15ab98133cfacf640b76d7fdf7890211110e5041.zip to repository.

Beginning build for Services application
----------------------------------------

<snip ...>

 2/2 [============================] 100%
```

If the `--force` option were provided in the example above, then all four builds would have executed and those artifacts would be stored in the repository.

### ./slarty do-deploys

Like most of the commands above, the `do-deploys` command accepts the `[-c|--config]` and `[-f|--fiter]` options. The purpose of the `do-deploys` command is to identify the archives that match the current repository's code state, download those from the repository, and unzip them into the `deploy_location` directory. If the archive cannot be found in the repository then it will be treated as a fatal error. This is to keep the steps of building and deploying strictly separated. Ideally, building happens on a Continuous Integration (CI) server while deployment would happen on the web or application server.

```
Found artifact slarty-source-15ab98133cfacf640b76d7fdf7890211110e5041.zip for source
 - Downloaded artifact
 - Unzipped artifact
 - Deleted (zip) artifact
Found artifact slarty-services-91f042b9df7c50b59ab08c657d09c81442e04a65.zip for Services
 - Downloaded artifact
 - Unzipped artifact
 - Deleted (zip) artifact
Found artifact slarty-models-51286ac4976b8dc1667d8f7bc033806e858cb7b7.zip for Models
 - Downloaded artifact
 - Unzipped artifact
 - Deleted (zip) artifact
Found artifact slarty-mess-f2788bbe13c3240951a10d97593467a68502e2f7.zip for AMess
 - Downloaded artifact
 - Unzipped artifact
 - Deleted (zip) artifact
```

The `do-deploy` process will create the directory structure specified in the `deploy_location` value. However, if that structure exists and contains files, it will not be cleared. That is a separate responsibility that should be taken care of elsewhere. The idea is that if an application needs to deploy several artifacts to the same place, it can do so. The unzipping command will overwrite any existing files that are in place when the deploy occurs. It will not remove any files that were already in place, so if a file existed in one deployment archive and then does not exist in the next, it would still exist in the deployment output directory.

### ./slarty deploy-assets 

The `deploy-assets` command accepts the `--filter` and `--config` options. They work the same as the other commands, except filter works on the name value in the config.

If you run `slarty deploy-assets` and Slarty is unable to find one of the referenced files in the artifact repository, the command will fail and tell you the asset that is missing. At this time, it does not "pre-check" for existence. It will work through the assets in order and it will fail on the first one that is missing. If it fails the return code of slarty will be non-zero.

The `deploy-assets` command will create the output directory path if it does not exist. It will be relative to the configured "root_directory" configuration option at the root.

## Why Slarty Bartfast?

The name of Slarty Bartfast comes from a character from The Hitchhiker's Guide to the Galaxy (HHGTTG). In the book, Slartibartfast works for works on the planet Magrathea, as a designer of custom planets. His favorite part of the job is designing coastlines and he won an award for the fjords in Norway. For Slartibartfast, planets are artifacts.

The name of the program went through a couple of ideas. The first two were "Artificer" and "Artifactory" but it turns out both of those are things that exist. Next was Slartibartifact which is fun to say but less fun to type as a command. Eventually I came back to "Slarty Bartfast" because of misremembering how to spell "Slartibartfast", but now it's totally on purpose because "./slarty" is simple enough and if this gets released, maybe searching for "Slarty Bartfast" will eventually get you to this program.

## Full Example Configuration

Below is a full example config for an app that has two separate builds and artifacts and is using S3 as its storage location for artifact archives.

```
{
  "application": "Sample Application",
  "root_directory": "__DIR__",
    "repository": {
    "adapter": "s3",
    "options": {
      "key": "AKABAKWAMBALAMBADOOP",
      "secret": "I+Kn0wS0m3th1ngY0uD0n+tKN0W4Ab0UTtRY2GU355",
      "region": "us-east-1",
      "bucket-name": "example-app-artifacts",
      "path-prefix": "Example/App"
    }
  },

  "artifacts": [
    {
      "name": "Purple App",
      "directories": ["src/Purple"],
      "command": "make srcPurple",
      "output_directory": "build/purple",
      "deploy_location": "public/purple",
      "artifact_prefix": "purple-app"
    },
        {
      "name": "Green App",
      "directories": ["src/Green"],
      "command": "make srcGren",
      "output_directory": "build/green",
      "deploy_location": "public/green",
      "artifact_prefix": "green-app"
    },
  ],
  
  "assets": [
      {
        "name": "Some Asset",
        "filename": "smurpy.tar.gz",
        "deploy_location": "build/assets/smurpy"
      },
      {
        "name": "Legitimate Business",
        "filename": "opposite_of_murder-4.2.tar.gz",
        "deploy_location": "build/assets/unicorns"
      }
    ]
}
```

## Local Mode
It is possible to use Slarty locally to run builds and test deployments. The `deploy-assets`, `do-builds`, `do-deploys`, and `should-build` commands all accept a `--local` flag which will act as though the artifacts.json has a repository adapter set to local. In order to help with this, I recommend you also include the repository->options->root value. The purpose of local mode is to allow for easier testing of builds and deployments without needing to change the artifacts.json file and then remember to not commit the changes to your source code repository. 

## Questions?
If there are any unanswered questions, problems, desired features, please contact slarty-support@davidstockton.com, or feel free to open a pull request.
