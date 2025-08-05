{
  description = "PHP Dev Shell";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-25.05";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs =
    {
      self,
      nixpkgs,
      flake-utils,
      ...
    }:
    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = import nixpkgs { inherit system; };

        phpWithExtensions = pkgs.php.buildEnv {
          extensions = { enabled, all }: enabled ++ (with all; [ pcov ]);
          # extraConfig = ''
          #   xdebug.mode=debug
          # '';
        };
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = with pkgs; [
            phpWithExtensions
            phpWithExtensions.packages.composer

            # Spell‐checking
            (aspellWithDicts (
              dicts: with dicts; [
                en
                # en-computers
                # en-science
              ]
            ))

            # Run GitHub Actions locally
            act

            # Docker CLI & daemon
            docker
            rootlesskit

            yarn
          ];

          # Optional: expose Docker socket from nix
          # This lets you run `docker …` without extra permissions
          shellHook = ''
            echo "🐘 PHP $(php -v | head -n1) ready"
            echo "📦 Composer: $(composer --version)"
            echo "📝 Aspell: $(aspell --version)"
            echo "🎡 act: $(act --version)"
            echo "🐳 docker: $(docker --version)"

            export DOCKER_HOST=unix://$XDG_RUNTIME_DIR/docker.sock

            # If you included dockerd, spin it up in the background:
            if ! pgrep -x dockerd > /dev/null; then
              echo "🚀 Starting rootless Docker daemon..."
              (dockerd-rootless > /dev/null 2>&1) &
            fi
          '';
        };
      }
    );
}
