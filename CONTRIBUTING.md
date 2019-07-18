# Contributing to Meraki

> Thank you for contributing!

## Table of Contents

* [Code of Conduct](#code-of-conduct)
* [I have a question](#i-have-a-question)
* [What should I know](#what-should-i-know)
* [How to contribute](how-to-contribute)
  * [Reporting a bug](#reporting-a-bug)
  * [Suggesting an enhancement/feature](#suggesting-an-enhancement/feature)
  * [Submitting a pull-request](#submitting-a-pull-request)
* [Styleguides](#styleguides)
  * [Git commit messages](#git-commit-messages)
  * [PHP source code](php-source-code)
  * [Tests](#tests)
  * [Documentation](#documentation)

## Code of Conduct

## I have a question

## What should I know

## How to contribute

### Reporting a bug

### Suggesting an enhancement/feature

### Submitting a pull-request

## Styleguides

### Git commit messages

All the rules and conventions described below are taken from this [article](https://chris.beams.io/posts/git-commit/) (as well as more in-depth information being provided), but are mostly considered best practices by the Git community.

#### 1. Separate subject from body with a blank line

Git treats everything up to the first blank line as the commit title. For example, when using the following commit message,

```cli
λ  git log
commit f59b02715063d8d71f04753a6fe242f792648e6f (HEAD -> master, origin/master)
Author: Nathan Bishop <nbish11@hotmail.com>
Date:   Mon May 20 15:06:34 2019 +1000

    Replace foo class into a foo and bar class

    The `Foo` class was doing to march bar'ing, violating the Single
    Responsibility Principle as pointed out in issue #14. Therefore, it was
    decided to move some of the Foo functionality into its own `Bar` class.

    Closes issue #14.
```

with the `--oneline` option, git will display this instead.

```cli
λ git log --oneline
f59b02 Replace foo class into a foo and bar class
```

#### 2. Limit the subject line to 50 characters

GitHub's UI will warn you if you go past 50 characters and will truncate the message if you go past 72 characters. In addition to the UI problem, this will enforce you to write more 'atomic' commits (one change or fix per commit) as you just don't have the space in the title to write a list of changes.

#### 3. Capitalize the subject line

Seems to be more of a convention than anything, but when viewing a list of commits or when git uses the first line of the commit message as the 'subject' heading of an email, it does look nicer.

#### 4. Do not end the subject line with a period

Or any other punctuation for that matter! It's uneccessary and space is at a premium. You don't want any unnecessary characters filling up that limit.

#### 5. Use the imperative mood in the subject line

Imperative mood basically means 'spoken or written as if giving a command or instruction'. A few examples:

* Clean your room
* Close the door
* Take out the trash

Another important thing to remember is that both Git and GitHub use the imperative mood in their own command line tools as well.

With ```git merge```

```
Merge branch 'myfeature'
```

or ```git revert```

```
Revert "Add the thing with the stuff"

This reverts commit cc87791524aedd593cff5a74532befe7ab69ce9d.
```

or when merging a pull request in GitHub

```
Merge pull request #123 from someuser/somebranch
```

A properly formed Git commit subject line should always be able to complete the following sentence:

> If applied, this commit will <your subject line here>

Good examples:

* If applied, this commit will **refactor subsystem X for readability**
* If applied, this commit will **update getting started documentation**
* If applied, this commit will **remove deprecated methods**
* If applied, this commit will **release version 1.0.0**
* If applied, this commit will **merge pull request #123 from user/branch**

Bad Examples:

* If applied, this commit will **fixed bug with Y**
* If applied, this commit will **changing behavior of X**
* If applied, this commit will **more fixes for broken stuff**
* If applied, this commit will **sweet new API methods**

> Note: Just remember that the imperative mood is only important in the commit message's subject line. You Do not need to use this rule in the body of a commit message.

#### 6. Wrap the body at 72 characters

#### 7. Use the body to explain what and why vs. how

### PHP source code

### Tests

### Documentation
