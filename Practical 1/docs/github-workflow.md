# GitHub Setup and Commit Workflow

## Create the repository
1. Sign in to GitHub and create a repository named `StudentHub`.
2. For a college project, add a short description and choose Public or Private according to your course requirements.
3. Open this extracted folder in VS Code.
4. Open the VS Code terminal in the `Practical 1` folder.

## First commit
Run the following commands, replacing the URL with your own repository URL:

```bash
git init
git add .
git commit -m "Practical 1: initialize StudentHub project"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/StudentHub.git
git push -u origin main
```

If Git is already initialized or a remote already exists, do not repeat those setup commands blindly. Check with `git status` and `git remote -v` first.

## After each practical
1. Make and test the changes.
2. Run `git status` to review changed files.
3. Stage the intended changes with `git add .` (or stage specific files).
4. Commit with a message describing the practical and feature, for example:

```bash
git add .
git commit -m "Practical 2: add semantic HTML pages"
git push
```

Keep commits small and descriptive. Do not commit passwords, API keys, database credentials, or generated environment files containing secrets.
