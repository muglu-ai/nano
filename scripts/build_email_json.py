import json
import pathlib
import re

RAW_EMAILS = """cmpatil@krishikalpa.com



secy-ipp@nic.in

sanjay.dubey@mp.gov.in
ps.snt@mp.gov.in

sanjeev.gupta@karnatakadigital.in

priyadarshi.mohapatra@curebay.com

priyadarshi.mohapatra@gmail.com

psharma@getepay.in

abhishek@sensegiz.com

nivedha@trashcon.in

lkatheeq@gmail.com

avnish.sabharwal@accenture.com
tina.philip@accenture.com

president@srisriuniversity.edu.in

sudhir@chiratae.com ; Divyam@chiratae.com

bhaskar.kalale@equalizercm.com

sindhu.gangadharan@sap.com

bhaskar@nasscom.in

"kdas2@jcp.com

Kaushik.Das@catalystbrands.com"

Amit_Kalra@swissre.com

kalavathi.gv@siemens-healthineers.com

ajay.vij@accenture.com

Manish.gupta@dell.com

lindraka@jaguarlandrover.com

vdhupar@nvidia.com

jaspreet@aiandbeyond.ai

Russ.Thomas@availity.com

mohit.saxena@inmobi.com

andrew.chin@alliancebernstein.com

Veeraraghavtm@gmail.com; Veeraraghav@ndtv.com

Subrata@accel.com

as@meity.gov.in

raghu@artpark.in

lvsubram@in.ibm.com

prayank@accel.com

as@meity.gov.in

vivek.atray@gmail.com

manu.saale@daimler.com

rajat.shrimal@incedoinc.com

viren.shetty@narayanahealth.org

Manoj@twimbit.com

Shaon.Sengupta@kyndryl.com

senthilspace@gmail.com

Parveen.chander@ihcltata.com

Aditya.Kankaria@airtel.com

shruthi.bopaiah@axisbank.com

rakesh.ranjan@kyndryl.com

"sds@cdac.in

edblr@cdac.in"

shikha.dahiya@gov.in

sngupta@sau.int

santoshd@trustgrid.com

chairmanoffice@isro.gov.in

gopi@myelinfoundry.com

arindam@iisc.ac.in

PublicDiplomacy@bangalore.mfa.gov.il

anchal@val-more.com

Guruprasad.S@in.bosch.com

naganand@ideaspringcap.com

ravi.iitkgp.ranjan@gmail.com

didci@maharashtra.gov.in

md@elcot.in

venk.krishnan@nuware.com

comm@bbmp.gov.in

existinchaos@gmail.com

kirthiga@verix.io

meerah@gmail.com

execasst@inktalks.com

President@fkcci.in

shreyak.c@anitab.org

Hem.kanwar@accenture.com

abansal@avaanacapital.com

execasst@inktalks.com

sonia.prashar@nm-india.com

Mary.overington@Austrade.gov.au

prithikasarathy@gmail.com

execasst@inktalks.com

neil@counterpointresearch.com

yvette.eechoud@minbuza.nl

s.shetty@pwc.com

rkhushu@ti.com

nitin.rajmohan@akeana.com

Radhika_Viswanathan@amat.com

Veerappan@tessolve.com

ashok.chandak@iesaonline.org

t.paul@kaynessemicon.com

tvyshali@micron.com

vivek.tyagi@analog.com

kannan.babu.ramia@intel.com

sridhar@outdu.com
sridhar.gs@outdu.com

Hitesh.garg@nxp.com

axel.sikora@hs-offenburg.de

manoj.kumar@st.com

Vijayaraghavan.Narayanan@infineon.com

shinto.joseph@ldra.com

Sushan.mahuli@lamresearch.com

kaushal.jadia@cyientdlm.com

diganta.sarma@inoxap.com

anil.kalra@ltsct.com

vivek.tyagi@analog.com

Andreas.Hammer@yageo.com

bhartendu.mishra@arrowasia.com

neeraj.pandita@wipro.com

Gagan.Bansal@tdk.com

ruchir.dixit@siemens.com

pradeep@7raysventures.com

ashish@maieuticsemi.com

gsubash@google.com

gani@yali.vc

ritesh.tyagi@wipro.com

rkhushu@ti.com

manoj@atomberg.com

satendra.singh@syrmasgs.com

anup.cheruvathoor@heroelectronix.com

Suraj_Rengarajan@amat.com

manjunath@kastech.in

t.paul@kaynessemicon.com

alok.jain@lamresearch.com

rao.tummala@ece.gatech.edu

Surya.Prekke@Intel.com

malini.moorthi.yf@renesas.com

mayank@iisc.ac.in

bhuvan.naik@tataelectronics.co.in

ceo@rbihub.in

anuj@onfinance.in

somesh@modussecure.com

dinesh.pai@zerodha.com

upasana@mobikwik.com

deepakshenoy@capitalmind.in

shrehith.k@joinditto.in

jay@supermileage.in

vyomika.singh@castler.com

anish@z47.com

vaibhav.tambe@transbnk.co.in

mohit@zerodha.com

Ayush@razorpay.com

ravi@blostem.com

dharmendra@rupeeflo.com

kuldeep@pensionbox.in

saranya.gopinath@razorpay.com

Mandeep@policybazaar.com

Alok@policybazaar.com

vasanth@smallcase.com

Priya@pay3.money

kalikab@microsoft.com

vivek@sarvam.ai

sashi@iisc.ac.in

ananth@gnani.ai

naveen@atimotors.com

ranjith@chiratae.com

derick.jose@accenture.com

abhijitlele@artpark.in


suri@ideaspringcap.com



vijaychandru@iisc.ac.in

paras@paraschopra.com

Nir@classiq.io

mekin@udhyam.org

meenakshi.g@rocketlearning.org

Rohit@artpark.in

acharya@jhu.edu

milos.maricic@gmail.com

ravi.jain@tdk-ventures.com

nagendra.nagaraja@qpiai.tech

ankush@corover.ai

karan.kirpalani@neysa.ai

shashankjoshi@economist.com

dhinesh.kana@fabheads.in

prashanth@accel.com

sssnath@gmail.com

vishesh.rajaram@specialeinvest.com

kshitij@pixxel.co.in

sunil@qnulabs.com

deepak@morphing.in

manu.iyer@bluehillcapital.in

anuj@jjgmachining.com

naman@airbound.co

yeshwanth@unmannd.com

Tanjores.balganesh@gmail.com

shridhar@peptris.com

aditya@avammune.com

Manjiri@oncostemdiagnostics.com

siraj@innaccel.com

niranjan.subbarao@gmail.com

a.kurpad@sjri.res.in

bipin@iombio.com

a.kurpad@sjri.res.in

gautambanerjee77@gmail.com

saher@rewisehealth.com

sujith@becknprotocol.io

harsh@carbonstrong.in

drvikramvishal@gmail.com

akshay@kazam.in

suruchisrao@ossusbio.com

ayush.b@exponent.energy

prasanta@newtrace.io

garima@enliteresearch.com

prithwish@greenaero.in

sanjeev@e3electric.ai

swapna@avaanacapital.com

anoop@moonrider.ai

Raghavendra.r@in.Bosch.com

kajal@dfi-india.com

hemanth@turno.club

shashi.kumar@akshayakalpa.org

shrutikutmutia@bacalt.bio

gowravbhargav@gmail.com

cmpatil@krishikalpa.com

saket.dave@wastelink.co

ravi.shiroor@stellapps.com

baka@phyx44.com

ceo@visionastraa.com

eshwer.shivaprakash@sunmobility.com

mahesh.babu@olectra.com

kamal.bali@volvo.com

mohal@matter.in

prashanth.doreswamy@continental-corporation.com

yedu@visionastraa.com

madan.padaki@outlook.com



kishore.p.durg@accenture.com



vc@vtu.ac.in

saraswathi.r@lightcast.io

swatirustagi1974@gmail.com

vicechancellor@dsu.edu.in

s.sadagopan@gmail.com

naganagouda@nhrdbangalore.com

Prochancellor@alliance.edu.in

savyasachi.srinivas@collins.com

srinivasraju@pisquaretech.com

anuj@zyoin.com

saraswathi.r@lightcast.io

sreedevi.mhegde@gmail.com

vc@jssuninoida.edu.in

vasuki.kashyap@mmactiv.com

priyachetty@me.com

vananthakrishnan@accel.com

vananthakrishnan@accel.com

neerja@pixxel.co.in; syed@pixxel.co.in

priyamvada.bhide@mmactiv.com

monish.babu@mmactiv.com

gurunath.angadi@mmactiv.com

hemalatha.br@mmactiv.com

mona.ebenezer@mmactiv.com

pooja.kalamkar@mmactiv.com

ss@culkey.org

priya@samhita.org

rajeesh@rubixstack.com

yamini.telkar@zinnovfoundation.org

prathima@goodpass.co 

udupidc@gmail.com

acs-tour@karnataka.gov.in 

secytourkar@gmail.com

sikta@pitchflix.tv

shane.smith@pitchflix.tv

itbtsec@karnataka.gov.in

mdkbits@gmail.com

avnish.sabharwal@accenture.com

jesper@exfinityventures.com

pulkit@unicornivc.com

rathnakar.s@hyderabadangels.in

sidhartha@eximiusvc.com

uday.kumar@saisoncapital.com

mansi@abyrocapital.com

founder@geniusinyou.one

c.tanasescu@latrobe.edu.au

mohammedyashik.b@livvolta.com

omkarbabu.k@gmail.com

microbeworks@microbeworksscientific.com

vaidy@charukesihealth.com

Sangeetha.krishnamoorthy@austrade.gov.au

c.tanasescu@latrobe.edu.au

karthik.mahesh@niti.gov.in

umanambiar@iiscmedicalschoolfoundation.org

director@ibab.ac.in

santoshd@trustgrid.com

joshua.craig@trustgrid.com

Sharanshs@trustgrid.com

arvindm@trustgrid.com

cmpatil@krishikalpa.com

arun@celesta.vc

sanjay.dubey@mp.gov.in

sanjeev.gupta@karnatakadigital.in

president@ableindia.org.in

kiran.mazumdar@biocon.com

debjani.g@gov.in

lsshashidhara@ncbs.res.in

Sanjay.Singh@gennova.bio

suban@ashoka.edu.in

mrinal.kammili@syngeneintl.com

sudha.rao@genotypic.co.in

taslim@ccamp.res.in

secretary@tdb.gov.in

ashish.venkataramani@eightroads.com

sailaja@sea6energy.com

arvind.venkatesan@crisprbits.com

ceo@itri.org.in

satya.dash@bigtec.co.in

bc@bigtec.co.in

debjani.paul@iitb.ac.in

anand@remidio.com

anuya@serigenmed.com

d.dendukuri@achiralabs.com

drvishal.rao@hcgel.com 

mammenchandy@gmail.com

ari.gargir@redcbiotech.com

andrea.raggi@istemrewind.com

prao@mac.com

arun@pandorumtechnologies.in

praveenv@instem.res.in

pmmurali@jananom.com

md.birac@nic.in

rajesh.krishnamurthy@laurus.bio

swami@sastra.edu

Vijay.Sudalaimuthu@biocon.com

sowmya@sea6energy.com

BLR@novonesis.com

kknaraya394@gmail.com

chairman@nabard.org

ajakoy@googlemail.com

gayatri@mssrf.res.in

gopal_icar@yahoo.co.in

ganesh@tranalab.com

subbiane@stringbio.com

ron.milo@weizmann.ac.il

PankajPatil@praj.net

gummadi@smail.iitm.ac.in

RJM@novonesis.com

swaraj.basu@strandls.com

tanay@mandrakebio.com

keerthi@thebioforge.com

nchandra@iisc.ac.in

arindam@iisc.ac.in

chandru@strandls.com

sweta.raghavan@sju.edu.in

mittur_jagadish@yahoo.co.in

paulm@microvioma.com

raghu@artpark.in

nitish@ultranutri.in

priyadarshi.mohapatra@curebay.com 

psharma@getepay.in

abhishek@sensegiz.com

nivedha@trashcon.in

lkatheeq@gmail.com

avnish.sabharwal@accenture.com

president@srisriuniversity.edu.in

bhaskar.kalale@equalizercm.com



chandraneel@chiratae.com

neeraja.rao@austrade.gov.au
Andrew.Collister@dfat.gov.au

ambika.banotra@nrwglobalb

kanchinb18@gmail.com
madhusoothanan.sm@indo-german.com

PublicDiplomacy@bangalore.mfa.gov.il 
cg-assistant@bangalore.mfa.gov.il

inaram@um.dk

dashmi_Parthan@jetro.go.jp
Naoto_Nakadate@jetro.go.jp

president.tienj@gmail.com

chetan.dixit@karnatakadigital.in

bharath.chandra@karnatakadigital.in

l.elijah.ext@ice.it

deepika.prithviraj@swissnex.org>

sid.naithani@businessfinland.fi

amanda.brock@openuk.uk 

pierre-1.beaudoin@diplomatie.gouv.fr

hlingam@globalbusinessinroads.com

lthomas@globalbusinessinroads.com

Rashmi.Priyesh@fcdo.gov.uk

aleena.joseph@minbuza.nl

marek.kijewski@msz.gov.pl 
rajraghuraj78@gmail.com

Silje.Christine.Andersen@mfa.no 
filippa.braarud@mfa.no

dsivasamy@hub.brussels 

anil.kumar@gbcprime.com 
juntaejung@gmail.com

jgullish@usibc.com

amchambangalore@amchamindia.com 

walderei@srtip.ae 

Sainath.Parayath@dubaichamber.com

abhijitsinh.Jadeja@dubaichamber.com

arghya.bose@accenture.com
ajay.vij@accenture.com

mkumar97@jaguarlandrover.com

rshenoy1@jaguarlandrover.com

resmi.nair@accenture.com

mac@aiandbeyond.ai

Sandeep.N@availity.com
Hina.S@availity.com
Vybhava.Srinivasan@availity.com

venk.krishnan@nuware.com

sanjay.tyagi@stpi.in

tithi.sarkar@mercedes-benz.com

akhil.kulangarth@mercedes-benz.com

naman.parcha@incedoinc.com

sudipta.bose@incedoinc.com"

Balamurali.Debur@kyndryl.com 

 Tinu.Thomas@kyndryl.com

harshibhat@deloitte.com

scientificsecretary@isro.gov.in

mganeshpillai@isro.gov.in

divya@myelinfoundry.com

Tal.BA@innovationisrael.org.il 

Prathiba.P@in.ey.com 

H.1@in.ey.com

anjali.nair@mmactiv.com"

bharat@val-more.com
manoj@val-more.com

Tal.BA@innovationisrael.org.il 

Prathiba.P@in.ey.com 

H.1@in.ey.com

anjali.nair@mmactiv.com"

bharat@val-more.com
manoj@val-more.com

jkameda@paloaltonetworks.com
mehnaaz@verix.io
kirthiga@optimizegeo.ai 
shagufta@verix.io

Neeraja.Rao@austrade.gov.au

reekchhanda.bose@pwc.com
s.shetty@pwc.com
aleena.joseph@minbuza.nl

manas.das@intel.com

manas.das@intel.com

katrin.reichwald@nxp.com

roman.ludin@st.com
yannis.martin@st.com
alain.quiniou@st.com
Sridhar.ETHIRAJ@st.com
olivier.lardy@st.com

Ulrike.Mittereder@infineon.com

andrew.banks@ldra.com

alok.jain@lamresearch.com

nitish.agrawal@inoxap.com

cheryl@inoxap.com

Rekha.H.ext@wipro.com

ashok.chandak@iesaonline.org

pradeep@7raysventures.com

Jasraj.dalvi@atomberg.com

ganesh.r@syrmasgs.com
amit.k@syrmasgs.com

Manitha_Shetty@contractor.amat.com

rishav.khemka.ud@renesas.com
chitra.hariharan.yh@renesas.com

ganapathiraman@vedacorp.com
venkat@vedacorp.com
manu.iyer@bluehillcapital.in

relinad@airbound.co
manu.iyer@bluehillcapital.in

nbopana@ccamp.res.in
taslim@ccamp.res.in

shreya@avaanacapital.com
swapna@avaanacapital.com

shreya@avaanacapital.com

arun.agarwal@moomark.in
ranjith.mukundan@stellapps.com

soa@dsu.edu.in
ea.vc@dsu.edu.in

girija.chandrashekar@alliance.edu.in
vc@alliance.edu.in

gauravnoronha@rbihub.in
aishwaryasrivastava@rbihub.in

dinesh.pai@zerodha.com
saranya.gopinath@razorpay.com

latha@z47.com

anish@z47.com
nimish.kadam@transbnk.co.in

dinesh.pai@zerodha.com
saranya.gopinath@razorpay.com

saranya.gopinath@razorpay.com

apeksha@policybazaar.com

apeksha@policybazaar.com
saranya.gopinath@razorpay.com

saranya.gopinath@razorpay.com

saranya.gopinath@razorpay.com

kavitha@sarvam.ai

saptarshi.sarkar@gnani.ai

chinmay.singh@atimotors.com

suma@chiratae.com
kailash@ideaspringcap.com

raghu@artpark.in

raghu@artpark.in

kailash@ideaspringcap.com

shirl@classiq.io
PublicDiplomacy@Bangalore.mfa.gov.il
raghu@artpark.in

raghu@artpark.in

deepika.prithviraj@swissnex.org
Prathiba.P@in.ey.com
lena.robra@swissnex.org
anjali.nair@mmactiv.com

vasan.churchill@tdk-ventures.com
kailash@ideaspringcap.com
raghu@artpark.in

swati.k@qpiai.tech
Kanishka.Agiwal@qpiai.tech

vidhi.jagwani@neysa.ai 
karan.kirpalani@neysa.ai
rahul@devc.com

Adil.Mohamed@austrade.gov.au 
Prathiba.P@in.ey.com

admin@nrwglobalbusiness.co.in 
Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

p.sahni.ext@ice.it 
Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

angeline.sophiecyril@businessfrance.fr

Prathiba.P@in.ey.com

Prathiba.P@in.ey.com

s.prasad1@imperial.ac.uk 
chandru.iyer@fcdo.gov.uk 
Prathiba.P@in.ey.com

Prathiba.P@in.ey.com 

Prathiba.P@in.ey.com
sac.partha@gmail.com

Prathiba.P@in.ey.com 

Prathiba.P@in.ey.com 
dgawankar@hub.brussels 

Prathiba.P@in.ey.com 

yjindal@usibc.com 
akaushik@usibc.com

jvarrier@srtip.ae

alisha@carvestartuplabs.com
vishnu@carvestartuplabs.com  
abhijitsinh.Jadeja@dubaichamber.com

vishnu@carvestartuplabs.com 
alisha@carvestartuplabs.com

ps.snt@mp.gov.in

"""

pattern = r"[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}"
# Extract, lower, and deduplicate emails
emails_set = {match.group(0).lower() for match in re.finditer(pattern, RAW_EMAILS)}
emails = sorted(emails_set)

# Build unique payload (emails are already unique due to set)
payload = [{"email": email, "status": "not_sent"} for email in emails]

output_path = pathlib.Path("public/helpTool/email_recipients.json")
output_path.parent.mkdir(parents=True, exist_ok=True)
output_path.write_text(json.dumps(payload, indent=2))

print(f"Saved {len(payload)} unique emails to {output_path}")

