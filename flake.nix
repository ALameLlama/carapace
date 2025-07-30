{
  description = "PHP 8.3 + Composer Dev Shell";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-24.05";
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
        pkgs = import nixpkgs {
          inherit system;
        };
        phpWithExtensions = pkgs.php83.buildEnv {
          extensions =
            { enabled, all }:
            enabled
            ++ (with all; [
              pcov
            ]);
          # Optional: Add extra PHP configuration
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
            (aspellWithDicts (
              dicts: with dicts; [
                en
                en-computers
                en-science
              ]
            ))
          ];

          shellHook = ''
            echo "🐘 PHP $(php -v | head -n1) ready"
            echo "📦 Composer version: $(composer --version)"
            echo "📝 Aspell Version: $(aspell --version)"
          '';
        };
      }
    );
}
