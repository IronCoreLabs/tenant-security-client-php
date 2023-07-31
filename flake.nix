{
  description = "Tenant Security Client PHP";

inputs = {
    nixpkgs.url = "nixpkgs/nixpkgs-unstable";
    utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, utils, nixpkgs, ... } @ inputs:
    utils.lib.eachDefaultSystem (system: let
      pkgs = nixpkgs.legacyPackages.${system};
    in rec {
      devShell = pkgs.mkShell {
        buildInputs = with pkgs; [php] ++ (with pkgs.phpPackages; [composer]);
      };
    });
}
