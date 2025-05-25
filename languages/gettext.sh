#---------------------------
# This script generates a new pmpro-generic-gateway.pot file for use in translations.
# To generate a new pmpro-generic-gateway.pot, cd to the main /pmpro-generic-gateway/ directory,
# then execute `languages/gettext.sh` from the command line.
# then fix the header info (helps to have the old pmpro-generic-gateway.pot open before running script above)
# then execute `cp languages/pmpro-generic-gateway.pot languages/pmprogpg.po` to copy the .pot to .po
# then execute `msgfmt languages/pmprogpg.po --output-file languages/pmprogpg.mo` to generate the .mo
#---------------------------
echo "Updating pmpro-generic-gateway.pot... "
xgettext -j -o languages/pmpro-generic-gateway.pot \
--default-domain=pmpro-generic-gateway \
--language=PHP \
--keyword=_ \
--keyword=__ \
--keyword=_e \
--keyword=_ex \
--keyword=_n \
--keyword=_x \
--sort-by-file \
--package-version=1.0 \
--msgid-bugs-address="jdavidcamejo@gmail.com" \
$(find . -name "*.php")
echo "Done!"
